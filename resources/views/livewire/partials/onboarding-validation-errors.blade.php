@if(!empty($onboardingModal) && $errors->any())
    <div role="alert" class="rounded-xl border border-rose-300 bg-rose-50 p-3 text-xs font-semibold text-rose-700">
        <p>Verifica os dados antes de guardar:</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
