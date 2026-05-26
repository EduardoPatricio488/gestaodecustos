<?php

namespace App\Livewire;

use App\Models\Investment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Http;

#[Layout('components.layouts.app')]
class InvestmentsHub extends Component
{
    public $marketData = [];
    public $symbol, $name, $quantity, $average_price, $type = 'Ação';

    public function mount()
    {
        $this->fetchMarketPrices();
    }

    /**
     * Procura preços reais de Cripto e simula Ações
     */
    public function fetchMarketPrices()
    {
        try {
            // Buscar Cripto Real (CoinGecko API - Gratuita)
            $cryptoResponse = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'bitcoin,ethereum,cardano,solana',
                'vs_currencies' => 'eur',
                'include_24hr_change' => 'true'
            ]);

            $prices = $cryptoResponse->json();

            $this->marketData = [
                'BTC' => ['name' => 'Bitcoin', 'price' => $prices['bitcoin']['eur'], 'change' => $prices['bitcoin']['eur_24h_change']],
                'ETH' => ['name' => 'Ethereum', 'price' => $prices['ethereum']['eur'], 'change' => $prices['ethereum']['eur_24h_change']],
                'SOL' => ['name' => 'Solana', 'price' => $prices['solana']['eur'], 'change' => $prices['solana']['eur_24h_change']],
                // Dados de Ações (Simulados - Refletindo valores reais atuais)
                'SPX' => ['name' => 'S&P 500', 'price' => 5222.68, 'change' => 0.45],
                'NVDA' => ['name' => 'NVIDIA', 'price' => 895.12, 'change' => 1.2],
                'AAPL' => ['name' => 'Apple', 'price' => 183.05, 'change' => -0.3],
            ];
        } catch (\Exception $e) {
            // Fallback caso a API falhe
            $this->marketData = [];
        }
    }

    public function save()
    {
        $this->validate([
            'symbol' => 'required',
            'quantity' => 'required|numeric',
            'average_price' => 'required|numeric',
        ]);

        Investment::create([
            'user_id' => auth()->id(),
            'workspace_id' => auth()->user()->current_workspace_id,
            'symbol' => strtoupper($this->symbol),
            'name' => $this->name ?: $this->symbol,
            'quantity' => $this->quantity,
            'average_price' => $this->average_price,
            'current_price' => $this->marketData[strtoupper($this->symbol)]['price'] ?? $this->average_price,
            'type' => $this->type,
        ]);

        $this->reset(['symbol', 'name', 'quantity', 'average_price']);
        $this->dispatch('modal-close', name: 'add-investment');
        $this->dispatch('toast', text: 'Investimento registado!');
    }

    public function render()
    {
        $investments = Investment::all();
        $totalInvested = $investments->sum(fn($i) => $i->quantity * $i->average_price);

        // Atualiza o preço atual baseado no mercado live
        foreach($investments as $inv) {
            if(isset($this->marketData[$inv->symbol])) {
                $inv->current_price = $this->marketData[$inv->symbol]['price'];
            }
        }

        $currentValue = $investments->sum(fn($i) => $i->quantity * $inv->current_price);

        return view('livewire.investments-hub', [
            'myAssets' => $investments,
            'totalInvested' => $totalInvested,
            'currentValue' => $currentValue,
            'profit' => $currentValue - $totalInvested
        ]);
    }
}
