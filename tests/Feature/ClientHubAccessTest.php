<?php

namespace Tests\Feature;

use App\Livewire\Business\ClientHub;
use App\Mail\ClientPortalAccessMail;
use App\Models\Client;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ClientHubAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_resend_a_clients_portal_access_code_by_email(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Empresa Teste',
            'owner_id' => $owner->id,
            'type' => 'business',
            'currency' => 'EUR',
        ]);
        $workspace->users()->attach($owner->id, ['role' => 'admin']);
        $owner->update(['current_workspace_id' => $workspace->id]);

        $client = Client::create([
            'user_id' => $owner->id,
            'workspace_id' => $workspace->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'status' => 'ativo',
            'portal_token' => str_repeat('a', 64),
        ]);

        $this->actingAs($owner);

        Livewire::test(ClientHub::class)
            ->call('resendPortalAccess', $client->id);

        Mail::assertSent(ClientPortalAccessMail::class, function (ClientPortalAccessMail $mail) use ($client) {
            return $mail->hasTo('cliente@example.com')
                && $mail->client->is($client)
                && $mail->token === $client->portal_token;
        });

        $client->refresh();
        $this->assertSame(hash('sha256', $client->portal_token), $client->portal_token_hash);
    }

    public function test_client_without_email_cannot_trigger_an_access_email(): void
    {
        Mail::fake();

        $owner = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Empresa Teste',
            'owner_id' => $owner->id,
            'type' => 'business',
            'currency' => 'EUR',
        ]);
        $workspace->users()->attach($owner->id, ['role' => 'admin']);
        $owner->update(['current_workspace_id' => $workspace->id]);

        $client = Client::create([
            'user_id' => $owner->id,
            'workspace_id' => $workspace->id,
            'name' => 'Cliente Sem Email',
            'status' => 'ativo',
            'portal_token' => str_repeat('b', 64),
        ]);

        $this->actingAs($owner);

        Livewire::test(ClientHub::class)
            ->call('resendPortalAccess', $client->id);

        Mail::assertNothingSent();
    }
}
