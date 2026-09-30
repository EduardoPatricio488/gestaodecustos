<?php

use App\Livewire\Public\SupplierDashboard;
use App\Models\Supplier;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

function supplierPortalFixture(): array
{
    $owner = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Workspace de Fornecedores',
        'type' => 'business',
        'owner_id' => $owner->id,
    ]);

    $supplierAToken = bin2hex(random_bytes(32));
    $supplierA = Supplier::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'name' => 'Fornecedor A',
        'portal_token_hash' => hash('sha256', $supplierAToken),
    ]);
    $supplierBToken = bin2hex(random_bytes(32));
    $supplierB = Supplier::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'name' => 'Fornecedor B',
        'portal_token_hash' => hash('sha256', $supplierBToken),
    ]);

    $ticketA = SupportTicket::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'supplier_id' => $supplierA->id,
        'subject' => 'Pedido do Fornecedor A',
    ]);
    $ticketB = SupportTicket::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'supplier_id' => $supplierB->id,
        'subject' => 'Pedido do Fornecedor B',
    ]);

    foreach ([
        [$ticketA, 'Mensagem do Fornecedor A'],
        [$ticketB, 'Mensagem do Fornecedor B'],
    ] as [$ticket, $message]) {
        DB::table('support_messages')->insert([
            'support_ticket_id' => $ticket->id,
            'user_id' => $owner->id,
            'message' => $message,
            'is_admin_reply' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $supplierA->setAttribute('portal_access_token', $supplierAToken);
    $supplierB->setAttribute('portal_access_token', $supplierBToken);

    return compact('supplierA', 'supplierB', 'ticketA', 'ticketB');
}

test('fornecedor A vê apenas os próprios tickets', function () {
    $fixture = supplierPortalFixture();

    $component = Livewire::test(SupplierDashboard::class, ['token' => $fixture['supplierA']->portal_access_token]);

    expect($component->viewData('tickets')->pluck('id')->all())
        ->toBe([$fixture['ticketA']->id]);
});

test('fornecedor A consegue responder ao próprio ticket', function () {
    $fixture = supplierPortalFixture();

    Livewire::test(SupplierDashboard::class, ['token' => $fixture['supplierA']->portal_access_token])
        ->set('activeTicketId', $fixture['ticketA']->id)
        ->set('replyMessage', 'Resposta do Fornecedor A')
        ->call('sendReply');

    expect(DB::table('support_messages')->where('support_ticket_id', $fixture['ticketA']->id)->where('message', 'Resposta do Fornecedor A')->exists())
        ->toBeTrue();
});

test('fornecedor A não consegue abrir o ticket do fornecedor B', function () {
    $fixture = supplierPortalFixture();

    expect(fn () => Livewire::test(SupplierDashboard::class, ['token' => $fixture['supplierA']->portal_access_token])
        ->call('setActiveTicket', $fixture['ticketB']->id))
        ->toThrow(ModelNotFoundException::class);
});

test('fornecedor A não consegue responder ao ticket do fornecedor B', function () {
    $fixture = supplierPortalFixture();

    expect(fn () => Livewire::test(SupplierDashboard::class, ['token' => $fixture['supplierA']->portal_access_token])
        ->set('activeTicketId', $fixture['ticketB']->id)
        ->set('replyMessage', 'Mensagem indevida')
        ->call('sendReply'))
        ->toThrow(ModelNotFoundException::class);

    expect(DB::table('support_messages')->where('support_ticket_id', $fixture['ticketB']->id)->where('message', 'Mensagem indevida')->exists())
        ->toBeFalse();
});

test('fornecedor A não consegue carregar mensagens do fornecedor B', function () {
    $fixture = supplierPortalFixture();

    expect(fn () => Livewire::test(SupplierDashboard::class, ['token' => $fixture['supplierA']->portal_access_token])
        ->set('activeTicketId', $fixture['ticketB']->id))
        ->toThrow(ModelNotFoundException::class);
});

test('fornecedor B continua a utilizar os próprios tickets normalmente', function () {
    $fixture = supplierPortalFixture();

    $component = Livewire::test(SupplierDashboard::class, ['token' => $fixture['supplierB']->portal_access_token])
        ->call('setActiveTicket', $fixture['ticketB']->id)
        ->set('replyMessage', 'Resposta do Fornecedor B')
        ->call('sendReply');

    expect($component->get('activeTicketId'))->toBe($fixture['ticketB']->id)
        ->and(DB::table('support_messages')->where('support_ticket_id', $fixture['ticketB']->id)->where('message', 'Resposta do Fornecedor B')->exists())->toBeTrue();
});
