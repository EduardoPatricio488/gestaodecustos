<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class NewsHub extends Component
{
    public string $regionFilter = 'ALL';

    public string $search = '';

    #[Computed]
    public function news(): array
    {
        $user = auth()->user();
        $workspaceId = $user?->current_workspace_id ?? 'none';

        return Cache::flexible("dashboard:news-24h:{$workspaceId}", [300, 900], function () {
            $feeds = [
                'PT' => 'https://news.google.com/rss/search?q=not%C3%ADcias+Portugal+when%3A1d&hl=pt-PT&gl=PT&ceid=PT%3Apt-PT',
                'INT' => 'https://news.google.com/rss/search?q=not%C3%ADcias+internacionais+when%3A1d&hl=pt-PT&gl=PT&ceid=PT%3Apt-PT',
            ];

            $articles = [];

            try {
                $responses = Http::pool(function (Pool $pool) use ($feeds) {
                    return [
                        'PT' => $pool->as('PT')->connectTimeout(3)->timeout(7)->withHeaders(['User-Agent' => 'FinanceProNews/1.0'])->get($feeds['PT']),
                        'INT' => $pool->as('INT')->connectTimeout(3)->timeout(7)->withHeaders(['User-Agent' => 'FinanceProNews/1.0'])->get($feeds['INT']),
                    ];
                });

                foreach (['PT', 'INT'] as $region) {
                    $response = $responses[$region] ?? null;
                    if (! $response || ! $response->successful()) {
                        continue;
                    }

                    $xml = @simplexml_load_string($response->body());
                    if (! $xml || ! isset($xml->channel->item)) {
                        continue;
                    }

                    foreach ($xml->channel->item as $item) {
                        try {
                            $publishedAt = \Illuminate\Support\Carbon::parse((string) $item->pubDate);
                        } catch (\Throwable $e) {
                            continue;
                        }

                        if ($publishedAt->lt(now()->subDay()) || $publishedAt->gt(now()->addMinutes(10))) {
                            continue;
                        }

                        $articles[] = [
                            'id' => $region.'-'.count($articles),
                            'region' => $region,
                            'title' => trim((string) $item->title),
                            'source' => trim((string) ($item->source ?? '')),
                            'url' => trim((string) $item->link),
                            'published_at' => $publishedAt->toIso8601String(),
                            'published_label' => $publishedAt->diffForHumans(),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Falha ao obter notícias das últimas 24 horas: '.$e->getMessage());
            }

            return collect($articles)
                ->filter(fn ($article) => $article['title'] !== '' && $article['url'] !== '')
                ->unique('url')
                ->sortByDesc('published_at')
                ->take(50)
                ->values()
                ->all();
        });
    }

    public function refreshNews(): void
    {
        $workspaceId = auth()->user()?->current_workspace_id ?? 'none';
        Cache::forget("dashboard:news-24h:{$workspaceId}");
        unset($this->news);
    }

    public function render()
    {
        $articles = collect($this->news)
            ->when($this->regionFilter !== 'ALL', fn ($items) => $items->where('region', $this->regionFilter))
            ->when(trim($this->search) !== '', function ($items) {
                $term = mb_strtolower(trim($this->search));

                return $items->filter(fn ($article) => str_contains(mb_strtolower($article['title'].' '.$article['source']), $term));
            })
            ->values();

        return view('livewire.news-hub', [
            'articles' => $articles,
            'totalNews' => count($this->news),
        ]);
    }
}
