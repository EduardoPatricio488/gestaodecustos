@if(session()->has('admin_impersonation'))
    <div class="fixed bottom-6 right-20 z-[999] animate-bounce">
        <form method="POST" action="{{ route('admin.stop-impersonating') }}">
            @csrf
            @method('DELETE')
            <button type="submit"
            class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-2xl transition-all no-underline"
        >
            <flux:icon name="arrow-left-on-rectangle" variant="micro" class="size-4" />
            Sair do Modo Colaborador
            </button>
        </form>
    </div>
@endif
