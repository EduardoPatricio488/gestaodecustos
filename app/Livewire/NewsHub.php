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
    public string $topicFilter = 'ALL';

    public string $search = '';

    #[Computed]
    public function news(): array
    {
        $user = auth()->user();
        $workspaceId = $user?->current_workspace_id ?? 'none';

        return Cache::flexible("dashboard:news-24h:{$workspaceId}", [300, 900], function () {
            $topics = [
                'PT' => 'notícias Portugal',
                'INT' => 'notícias internacionais',
                'FOOTBALL' => 'futebol Liga Portugal Champions League seleção',
                'SPORTS' => 'desporto modalidades atletismo ténis basquetebol',
                'ECONOMY' => 'economia mercados empresas finanças',
                'TECH' => 'tecnologia inteligência artificial inovação cibersegurança',
                'POLITICS' => 'política Governo eleições parlamento',
                'SCIENCE' => 'ciência investigação descobertas espaço',
                'ENTERTAINMENT' => 'entretenimento cultura cinema música televisão',
            ];

            $feeds = collect($topics)->mapWithKeys(fn ($query, $topic) => [
                $topic => 'https://news.google.com/rss/search?q='.rawurlencode($query.' when:1d').'&hl=pt-PT&gl=PT&ceid=PT:pt-PT',
            ])->all();

            $articles = [];

            try {
                $responses = Http::pool(function (Pool $pool) use ($feeds) {
                    foreach ($feeds as $topic => $url) {
                        $pool->as($topic)->connectTimeout(3)->timeout(7)->withHeaders(['User-Agent' => 'FinanceProNews/1.0'])->get($url);
                    }
                });

                foreach (array_keys($feeds) as $topic) {
                    $response = $responses[$topic] ?? null;
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

                        $description = trim((string) ($item->description ?? ''));
                        $media = $item->children('media', true);
                        $imageUrl = trim((string) ($item->enclosure['url'] ?? ''));
                        $thumbnail = $media->thumbnail ?? null;
                        $mediaContent = $media->content ?? null;

                        if ($imageUrl === '' && $thumbnail) {
                            $imageUrl = trim((string) ($thumbnail->attributes()['url'] ?? ''));
                        }

                        if ($imageUrl === '' && $mediaContent) {
                            $imageUrl = trim((string) ($mediaContent->attributes()['url'] ?? ''));
                        }

                        $descriptionHtml = html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                        if ($imageUrl === '' && preg_match("~<img[^>]+src=['\\\"]([^'\\\"]+)['\\\"]~i", $descriptionHtml, $imageMatch)) {
                            $imageUrl = trim($imageMatch[1]);
                        }

                        $imageUrl = filter_var($imageUrl, FILTER_VALIDATE_URL)
                            && in_array(strtolower((string) parse_url($imageUrl, PHP_URL_SCHEME)), ['http', 'https'], true)
                                ? $imageUrl
                                : null;

                        $summary = trim(preg_replace('/\\s+/', ' ', strip_tags($descriptionHtml)));
                        $summary = html_entity_decode($summary, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $summary = mb_substr($summary, 0, 320);

                        $articles[] = [
                            'id' => $topic.'-'.count($articles),
                            'topic' => $topic,
                            'title' => trim((string) $item->title),
                            'summary' => $summary,
                            'image' => $imageUrl,
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
            ->when($this->topicFilter !== 'ALL', fn ($items) => $items->where('topic', $this->topicFilter))
            ->when(trim($this->search) !== '', function ($items) {
                $term = mb_strtolower(trim($this->search));

                return $items->filter(fn ($article) => str_contains(mb_strtolower($article['title'].' '.$article['source'].' '.$article['summary']), $term));
            })
            ->values();

        return view('livewire.news-hub', [
            'articles' => $articles,
            'totalNews' => count($this->news),
        ]);
    }
}
