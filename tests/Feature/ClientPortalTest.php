<?php

use App\Livewire\ClientPortal;
use App\Models\Client;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;

function clientPortalFixture(): array
{
    $owner = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Workspace de Teste',
        'type' => 'business',
        'owner_id' => $owner->id,
    ]);

    $clientA = Client::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'name' => 'Cliente A',
        'email' => 'cliente-a@example.com',
        'portal_token' => Str::random(48),
    ]);
    $clientB = Client::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'name' => 'Cliente B',
        'email' => 'cliente-b@example.com',
        'portal_token' => Str::random(48),
    ]);

    $ticketA = SupportTicket::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'client_id' => $clientA->id,
        'subject' => 'Pedido do Cliente A',
    ]);
    $ticketB = SupportTicket::create([
        'user_id' => $owner->id,
        'workspace_id' => $workspace->id,
        'client_id' => $clientB->id,
        'subject' => 'Pedido do Cliente B',
    ]);

    DB::table('support_messages')->insert([
        'support_ticket_id' => $ticketA->id,
        'user_id' => $owner->id,
        'message' => 'Mensagem do Cliente A',
        'is_admin_reply' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('support_messages')->insert([
        'support_ticket_id' => $ticketB->id,
        'user_id' => $owner->id,
        'message' => 'Mensagem do Cliente B',
        'is_admin_reply' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return compact('clientA', 'clientB', 'ticketA', 'ticketB');
}

test('cliente consegue ver apenas os próprios tickets', function () {
    $fixture = clientPortalFixture();

    $component = Livewire::test(ClientPortal::class, ['token' => $fixture['clientA']->portal_token]);

    expect($component->viewData('tickets')->pluck('id')->all())
        ->toBe([$fixture['ticketA']->id]);
});

test('cliente consegue responder ao próprio ticket', function () {
    $fixture = clientPortalFixture();

    Livewire::test(ClientPortal::class, ['token' => $fixture['clientA']->portal_token])
        ->set('activeTicketId', $fixture['ticketA']->id)
        ->set('replyMessage', 'Resposta do Cliente A')
        ->call('sendReply');

    expect(DB::table('support_messages')->where('support_ticket_id', $fixture['ticketA']->id)->where('message', 'Resposta do Cliente A')->exists())
        ->toBeTrue();
});

test('cliente não consegue abrir o ticket de outro cliente', function () {
    $fixture = clientPortalFixture();

    expect(fn () => Livewire::test(ClientPortal::class, ['token' => $fixture['clientA']->portal_token])
        ->call('setActiveTicket', $fixture['ticketB']->id))
        ->toThrow(ModelNotFoundException::class);
});

test('cliente não consegue enviar mensagem para ticket de outro cliente', function () {
    $fixture = clientPortalFixture();

    expect(fn () => Livewire::test(ClientPortal::class, ['token' => $fixture['clientA']->portal_token])
        ->set('activeTicketId', $fixture['ticketB']->id)
        ->set('replyMessage', 'Mensagem indevida')
        ->call('sendReply'))
        ->toThrow(ModelNotFoundException::class);

    expect(DB::table('support_messages')->where('support_ticket_id', $fixture['ticketB']->id)->where('message', 'Mensagem indevida')->exists())
        ->toBeFalse();
});

test('cliente não consegue carregar mensagens de outro cliente', function () {
    $fixture = clientPortalFixture();

    expect(fn () => Livewire::test(ClientPortal::class, ['token' => $fixture['clientA']->portal_token])
        ->set('activeTicketId', $fixture['ticketB']->id))
        ->toThrow(ModelNotFoundException::class);
});

test('cliente B continua a utilizar os próprios tickets normalmente', function () {
    $fixture = clientPortalFixture();

    $component = Livewire::test(ClientPortal::class, ['token' => $fixture['clientB']->portal_token])
        ->call('setActiveTicket', $fixture['ticketB']->id)
        ->set('replyMessage', 'Resposta do Cliente B')
        ->call('sendReply');

    expect($component->get('activeTicketId'))->toBe($fixture['ticketB']->id)
        ->and(DB::table('support_messages')->where('support_ticket_id', $fixture['ticketB']->id)->where('message', 'Resposta do Cliente B')->exists())->toBeTrue();
});
