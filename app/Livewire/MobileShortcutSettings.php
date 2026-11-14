<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MobileShortcutSettings extends Component
{
    public bool $open = false;

    public array $shortcuts = [];

    public bool $dividerEnabled = true;

    public string $dividerAfter = 'investments';

    private function defaults(): array
    {
        return [
            'expense' => ['label' => 'Nova despesa', 'enabled' => true, 'order' => 1],
            'income' => ['label' => 'Nova receita', 'enabled' => true, 'order' => 2],
            'movements' => ['label' => 'Importar Movimentos', 'enabled' => true, 'order' => 3],
            'investments' => ['label' => 'Novo investimento', 'enabled' => true, 'order' => 4],
            'bank' => ['label' => 'Banco', 'enabled' => true, 'order' => 5],
            'goal' => ['label' => 'Novo objetivo', 'enabled' => true, 'order' => 6],
            'reminder' => ['label' => 'Novo lembrete', 'enabled' => true, 'order' => 7],
            'subscription' => ['label' => 'Nova assinatura', 'enabled' => true, 'order' => 8],
            'fitness' => ['label' => 'Zona de Treino', 'enabled' => true, 'order' => 9],
            'calendar' => ['label' => 'Calendário', 'enabled' => true, 'order' => 10],
            'connect' => ['label' => 'Finance Connect', 'enabled' => true, 'order' => 11],
        ];
    }

    public function mount(): void
    {
        $saved = auth()->user()->mobile_shortcuts ?? [];
        $saved = is_array($saved) ? $saved : [];
        $divider = $saved['_divider'] ?? [];
        unset($saved['_divider']);
        $this->shortcuts = array_replace_recursive($this->defaults(), $saved);
        $this->dividerEnabled = (bool) ($divider['enabled'] ?? true);
        $this->dividerAfter = (string) ($divider['after'] ?? 'investments');
        $this->normalizeOrder();
    }

    public function openSettings(): void
    {
        $saved = auth()->user()->mobile_shortcuts ?? [];
        $saved = is_array($saved) ? $saved : [];
        $divider = $saved['_divider'] ?? [];
        unset($saved['_divider']);
        $this->shortcuts = array_replace_recursive($this->defaults(), $saved);
        $this->dividerEnabled = (bool) ($divider['enabled'] ?? true);
        $this->dividerAfter = (string) ($divider['after'] ?? 'investments');
        $this->normalizeOrder();
        $this->open = true;
    }

    public function move(string $key, int $direction): void
    {
        abort_unless(array_key_exists($key, $this->shortcuts), 404);

        $ordered = collect($this->shortcuts)->sortBy('order')->keys()->values()->all();
        $index = array_search($key, $ordered, true);
        $target = $index + ($direction < 0 ? -1 : 1);

        if ($index === false || ! isset($ordered[$target])) {
            return;
        }

        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        foreach ($ordered as $position => $shortcutKey) {
            $this->shortcuts[$shortcutKey]['order'] = $position + 1;
        }
    }

    public function save(): void
    {
        $defaults = $this->defaults();

        foreach ($defaults as $key => $default) {
            $this->shortcuts[$key]['label'] = trim((string) ($this->shortcuts[$key]['label'] ?? $default['label']));
            $this->shortcuts[$key]['label'] = mb_substr($this->shortcuts[$key]['label'] ?: $default['label'], 0, 32);
            $this->shortcuts[$key]['enabled'] = (bool) ($this->shortcuts[$key]['enabled'] ?? true);
        }

        $this->normalizeOrder();
        if (! array_key_exists($this->dividerAfter, $defaults)) {
            $this->dividerAfter = 'investments';
        }
        $this->shortcuts['_divider'] = [
            'enabled' => $this->dividerEnabled,
            'after' => $this->dividerAfter,
        ];

        DB::table('users')->where('id', auth()->id())->update([
            'mobile_shortcuts' => json_encode($this->shortcuts, JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        $this->open = false;
        $this->dispatch('mobile-shortcuts-saved');
        $this->redirect(route('mobile.home'), navigate: true);
    }

    private function normalizeOrder(): void
    {
        $ordered = collect($this->shortcuts)->sortBy('order')->keys()->values();

        foreach ($ordered as $position => $key) {
            $this->shortcuts[$key]['order'] = $position + 1;
        }
    }

    public function render()
    {
        return view('livewire.mobile-shortcut-settings', [
            'shortcutNames' => [
                'expense' => 'Despesas',
                'income' => 'Receitas',
                'movements' => 'Movimentos',
                'investments' => 'Investimentos',
                'bank' => 'Banco',
                'goal' => 'Objetivos',
                'reminder' => 'Lembretes',
                'subscription' => 'Assinaturas',
                'fitness' => 'Zona de Treino',
                'calendar' => 'Calendário',
                'connect' => 'Finance Connect',
            ],
        ]);
    }
}
