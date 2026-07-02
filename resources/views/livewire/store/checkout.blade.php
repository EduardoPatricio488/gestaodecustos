<div class="max-w-4xl mx-auto py-10 space-y-8">

    {{-- PROGRESS BAR --}}
    @php $steps = [1 => 'Resumo', 2 => 'Pagamento', 3 => 'Confirmação']; @endphp
    <div class="flex items-center justify-between">
        @foreach($steps as $num => $label)
            <div class="flex-1 flex flex-col items-center">
                <div @class([
                    'size-10 rounded-full flex items-center justify-center font-black text-sm border-2 transition-all',
                    'bg-brand-600 border-brand-600 text-white' => $step >= $num,
                    'bg-white border-zinc-300 text-zinc-400' => $step < $num,
                ])>{{ $num }}</div>
                <span class="text-[9px] font-black uppercase tracking-widest mt-2 {{ $step >= $num ? 'text-brand-600' : 'text-zinc-400' }}">{{ $label }}</span>
            </div>
            @if(!$loop->last)
                <div class="flex-1 h-0.5 mx-2 {{ $step > $num ? 'bg-brand-600' : 'bg-zinc-200' }}"></div>
            @endif
        @endforeach
    </div>

    <div class="bg-zinc-50 border border-zinc-200 rounded-[3rem] p-8 shadow-xl">

        {{-- STEP 1: RESUMO --}}
        @if($step === 1)
            <h2 class="text-2xl font-black uppercase italic mb-6">Resumo do Pedido</h2>
            <div class="space-y-4 mb-6">
                @foreach($items as $item)
                    <div class="flex items-center gap-4 p-4 bg-white rounded-2xl border" wire:key="item-{{ $item['product']->id }}">
                        <span class="text-3xl">{{ $item['product']->image }}</span>
                        <div class="flex-1">
                            <p class="font-black uppercase text-sm">{{ $item['product']->title }}</p>
                            <p class="text-xs text-zinc-500">{{ $item['quantity'] }}x {{ number_format($item['product']->price, 2, ',', '.') }} €</p>
                        </div>
                        <p class="font-black">{{ number_format($item['subtotal'], 2, ',', '.') }} €</p>
                    </div>
                @endforeach
            </div>

            @if($crossSell->isNotEmpty())
                <div class="mb-6 p-4 bg-brand-50 border border-brand-200 rounded-2xl">
                    <p class="text-[10px] font-black uppercase text-brand-700 mb-3">Também poderás gostar</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($crossSell as $product)
                            <button wire:click="addToCart({{ $product->id }})" class="px-3 py-2 bg-white border rounded-xl text-[9px] font-bold">
                                + {{ $product->title }} ({{ number_format($product->price, 2, ',', '.') }} €)
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex justify-between items-center">
                <span class="text-2xl font-black">{{ number_format($subtotal, 2, ',', '.') }} €</span>
                <flux:button wire:click="nextStep" variant="primary" class="rounded-2xl font-black uppercase text-[10px]">Continuar →</flux:button>
            </div>
        @endif

        {{-- STEP 2: PAGAMENTO --}}
        @if($step === 2)
            <h2 class="text-2xl font-black uppercase italic mb-6">Pagamento</h2>

            <div class="space-y-4 mb-6">
                <label @class(['block p-4 border-2 rounded-2xl cursor-pointer', 'border-brand-500 bg-brand-50' => $paymentMethod === 'simulated'])>
                    <input type="radio" wire:model.live="paymentMethod" value="simulated" class="sr-only" />
                    <span class="font-bold">Saldo em Conta / MB WAY (Simulação)</span>
                </label>
            </div>

            <div class="p-4 bg-white border rounded-2xl mb-6">
                <p class="text-[10px] font-black uppercase text-zinc-500 mb-2">Cupão de desconto</p>
                @if($appliedCoupon)
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-emerald-600">{{ $appliedCoupon->code }} aplicado (-{{ number_format($discount, 2, ',', '.') }} €)</span>
                        <button wire:click="removeCoupon" class="text-xs text-red-500 font-bold">Remover</button>
                    </div>
                @else
                    <div class="flex gap-2">
                        <input wire:model="couponCode" type="text" placeholder="Código promocional"
                               class="flex-1 px-4 py-2 border rounded-xl text-sm uppercase" />
                        <button wire:click="applyCoupon" class="px-4 py-2 bg-zinc-900 text-white rounded-xl text-[10px] font-black uppercase">Aplicar</button>
                    </div>
                @endif
            </div>

            <div class="flex justify-between">
                <button wire:click="prevStep" class="text-[10px] font-black uppercase text-zinc-500">← Voltar</button>
                <flux:button wire:click="nextStep" variant="primary" class="rounded-2xl font-black uppercase text-[10px]">Rever Pedido →</flux:button>
            </div>
        @endif

        {{-- STEP 3: CONFIRMAÇÃO --}}
        @if($step === 3)
            <h2 class="text-2xl font-black uppercase italic mb-6">Confirmar Compra</h2>

            <div class="p-6 bg-white border rounded-2xl mb-6 space-y-2">
                <div class="flex justify-between text-sm"><span>Subtotal</span><span>{{ number_format($subtotal, 2, ',', '.') }} €</span></div>
                @if($discount > 0)
                    <div class="flex justify-between text-sm text-emerald-600"><span>Desconto</span><span>-{{ number_format($discount, 2, ',', '.') }} €</span></div>
                @endif
                <div class="flex justify-between text-xl font-black pt-2 border-t"><span>Total</span><span>{{ number_format($total, 2, ',', '.') }} €</span></div>
            </div>

            @if($upsell->isNotEmpty())
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                    <p class="text-[10px] font-black uppercase text-amber-700 mb-2">Upgrade recomendado</p>
                    @foreach($upsell->take(1) as $product)
                        <button wire:click="addToCart({{ $product->id }})" class="text-sm font-bold text-amber-800">
                            + {{ $product->title }} por apenas {{ number_format($product->price, 2, ',', '.') }} €
                        </button>
                    @endforeach
                </div>
            @endif

            <label @class([
                'flex items-start gap-4 p-5 mb-6 rounded-2xl border-2 cursor-pointer transition-all',
                'bg-emerald-50 border-emerald-400 shadow-sm shadow-emerald-500/10' => $addExpenseToEducation,
                'bg-white border-zinc-200' => ! $addExpenseToEducation,
            ])>
                <input type="checkbox" wire:model.live="addExpenseToEducation" class="mt-1 size-4 rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500" />
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <flux:icon name="academic-cap" class="size-4 text-emerald-600" />
                        <p class="font-black uppercase text-[10px] tracking-widest text-emerald-800">
                            Adicionar despesa à categoria Educação
                        </p>
                        @if($addExpenseToEducation)
                            <span class="text-[8px] font-black uppercase bg-emerald-600 text-white px-2 py-0.5 rounded-full">Ativo</span>
                        @endif
                    </div>
                    <p class="text-xs text-emerald-900/80 mt-1.5 font-medium italic">
                        Regista automaticamente o valor da compra no hub de Educação (subcategoria Formação).
                    </p>
                </div>
            </label>

            <div class="flex flex-col sm:flex-row gap-3">
                <button wire:click="prevStep" class="flex-1 h-12 border rounded-2xl font-black uppercase text-[10px]">← Voltar</button>
                <flux:button wire:click="confirmPurchase" variant="primary" class="flex-1 h-12 rounded-2xl font-black uppercase text-[10px] shadow-xl">
                    Confirmar e Ativar
                </flux:button>
            </div>
        @endif

    </div>
</div>
