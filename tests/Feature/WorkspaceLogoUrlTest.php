<?php

use App\Models\Workspace;

test('o url da logo da empresa remove duplicacao de storage e resolve corretamente', function () {
    $workspace = new Workspace([
        'name' => 'Empresa Teste',
        'logo_path' => 'storage/logos/test-logo.png',
    ]);

    expect($workspace->logo_url)
        ->not->toContain('storage/storage')
        ->toContain('/storage/logos/test-logo.png');

    $workspace2 = new Workspace([
        'name' => 'Empresa Teste 2',
        'logo_path' => 'logos/test-logo-2.png',
    ]);

    expect($workspace2->logo_url)
        ->not->toContain('storage/storage')
        ->toContain('/storage/logos/test-logo-2.png');
});
