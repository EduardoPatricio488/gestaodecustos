<div class="max-w-xl mx-auto py-8 px-4">

    {{-- Cabeçalho --}}
    <div class="mb-6 flex items-center gap-3">
        <button wire:click="cancel"
                class="text-gray-500 hover:text-gray-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $isEditing ? 'Editar Despesa' : 'Nova Despesa' }}
        </h1>
    </div>

    {{-- Flash --}}
    @if (session('ok'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
            {{ session('ok') }}
        </div>
    @endif

    <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">

        {{-- Valor --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Valor <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium">€</span>
                <input wire:model="amount"
                       type="number"
                       step="0.01"
                       min="0.01"
                       placeholder="0,00"
                       class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('amount') border-red-400 @enderror">
            </div>
            @error('amount')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Descrição --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descrição
            </label>
            <input wire:model="description"
                   type="text"
                   placeholder="Ex: Almoço de equipa"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('description') border-red-400 @enderror">
            @error('description')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Categoria --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Categoria
            </label>
            <select wire:model="category_id"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('category_id') border-red-400 @enderror">
                <option value="">— Sem categoria —</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">
                        {{ $cat->icon ? $cat->icon . ' ' : '' }}{{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Data --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Data <span class="text-red-500">*</span>
            </label>
            <input wire:model="spent_at"
                   type="date"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-gray-800 @error('spent_at') border-red-400 @enderror">
            @error('spent_at')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Botões --}}
        <div class="flex gap-3 pt-2">
            <button type="button"
                    wire:click="cancel"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button type="submit"
                    class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 active:scale-95 transition">
                {{ $isEditing ? 'Guardar alterações' : 'Criar despesa' }}
            </button>
        </div>

    </form>

</div>
