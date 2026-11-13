<?php

namespace Tests\Feature;

use App\Exceptions\AiProviderException;
use App\Livewire\AiControlCenter;
use App\Livewire\AiInsights;
use App\Livewire\Profile\AiMemorySettings;
use App\Models\AiActionLog;
use App\Models\AiInsight;
use App\Models\AiMemory;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Investment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use App\Services\AI\AiBrainService;
use App\Services\AI\FinancialIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class AiBrainAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->workspace = Workspace::factory()->create(['owner_id' => $this->user->id, 'type' => 'personal']);
        $this->user->workspaces()->syncWithoutDetaching([$this->workspace->id => ['role' => 'owner']]);
        $this->user->forceFill(['current_workspace_id' => $this->workspace->id])->save();
        $this->actingAs($this->user);
        config(['services.openrouter.api_key' => 'test-key', 'services.openrouter.model' => 'openrouter/auto']);
    }

    private function exampleReport(): string
    {
        return "**Resultados**\n"
            ."- Os registos disponíveis permitem uma análise inicial prudente.\n"
            ."- Os investimentos devem ser avaliados de acordo com os dados disponíveis.\n\n"
            ."**O que penso**\n"
            ."- As valorizações registadas são positivas, mas não garantem resultados futuros.\n"
            ."- As despesas atuais podem incluir valores pontuais que exigem confirmação.\n\n"
            ."**Cenários**\n"
            ."- A evolução depende dos rendimentos e dos compromissos ainda por confirmar.\n\n"
            ."**Pontos fortes**\n"
            ."- Os investimentos registados permitem acompanhar a evolução da carteira.\n\n"
            ."**Pontos negativos**\n"
            ."- A falta de dados confirmados limita a avaliação dos riscos.\n\n"
            ."**O que aconselho**\n"
            ."- Confirma os rendimentos recebidos antes de assumir que estão disponíveis.\n"
            ."- Revê os objetivos e define prioridades para os próximos meses.\n\n"
            ."**Resumo**\n"
            .'- Analisa os resultados e confirma os dados em falta antes de decidir.';
    }

    public function test_report_and_chat_use_free_models_even_when_another_model_is_configured(): void
    {
        Http::fake([
            'openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => $this->exampleReport()]]],
                'usage' => ['total_tokens' => 12],
            ]),
        ]);

        $brain = app(AiBrainService::class);
        $this->assertSame($this->exampleReport(), $brain->generateExecutiveReport($this->user, $this->workspace)['content']);
        $this->assertSame($this->exampleReport(), $brain->chat($this->user, 'Olá')['content']);

        Http::assertSentCount(2);
        Http::assertSent(fn (Request $request) => $request['model'] === 'google/gemma-4-31b-it:free'
            && $request['models'] === ['openrouter/free']
            && data_get($request->data(), 'provider.max_price.prompt') === 0
            && data_get($request->data(), 'provider.max_price.completion') === 0
            && ! array_key_exists('tools', $request->data()));
        Http::assertSent(fn (Request $request) => $request['model'] === 'openrouter/free'
            && array_key_exists('tools', $request->data()));
    }

    public function test_provider_limit_is_explained_and_previous_report_is_preserved(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([], 402)]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('Verifica os limites da chave.')
            ->assertDispatched('cfo-report-finished')
            ->assertNotDispatched('toast');

        $this->assertSame(0, AiActionLog::count());
    }

    public function test_successful_report_is_shown_and_recorded(): void
    {
        Mail::fake();
        Http::fake([
            'openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => $this->exampleReport()]]],
                'usage' => ['total_tokens' => 12],
            ]),
        ]);

        Livewire::test(AiInsights::class)
            ->call('generateInsights')
            ->assertSet('analysisError', null)
            ->assertDispatched('cfo-report-finished')
            ->assertSee('Os registos disponíveis permitem uma análise inicial prudente.')
            ->assertSee('Cenários')
            ->assertSee('Pontos fortes')
            ->assertSee('Pontos negativos')
            ->assertSee('O que aconselho')
            ->assertSee('Resumo')
            ->assertSeeHtml('<h3>Resultados</h3>')
            ->assertSeeHtml('<h3>Cenários</h3>')
            ->assertSeeHtml('<h3>O que aconselho</h3>')
            ->assertSeeHtml('<h3>Resumo</h3>')
            ->assertDontSee('Verificações de Segurança')
            ->assertSeeHtml('wire:name="ai-control-center"');

        $this->assertDatabaseHas('ai_action_logs', [
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'tool_name' => 'generate_executive_report',
            'status' => 'completed',
        ]);
    }

    public function test_rate_limit_has_a_clear_error(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([], 429)]);

        $this->expectException(AiProviderException::class);
        $this->expectExceptionMessage('O limite de pedidos foi atingido');
        app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);
    }

    public function test_report_retries_http_400_with_simplified_free_request(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['error' => ['message' => 'Invalid request parameters']], 400)
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $this->exampleReport()]]]])]);

        $this->assertSame($this->exampleReport(), app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(2);
        $requests = Http::recorded();
        $this->assertSame($requests[0][0]['messages'], $requests[1][0]['messages']);
        $this->assertSame('openrouter/free', $requests[1][0]['model']);
        $this->assertArrayNotHasKey('models', $requests[1][0]->data());
        $this->assertArrayNotHasKey('reasoning', $requests[1][0]->data());
        $this->assertSame(0, data_get($requests[1][0]->data(), 'provider.max_price.prompt'));
        $this->assertSame(0, data_get($requests[1][0]->data(), 'provider.max_price.completion'));
    }

    public function test_http_400_on_second_report_attempt_can_use_compatibility_retry(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'length', 'message' => ['content' => 'Relatório incompleto']]]])
            ->push(['error' => ['message' => 'Invalid request parameters']], 400)
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $this->exampleReport()]]]])]);

        $this->assertSame($this->exampleReport(), app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(3);
        $requests = Http::recorded();
        $this->assertSame($requests[1][0]['messages'], $requests[2][0]['messages']);
        $this->assertArrayNotHasKey('reasoning', $requests[2][0]->data());
    }

    public function test_repeated_http_400_preserves_previous_report_and_explains_the_error(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response(['error' => ['message' => 'Invalid request parameters']], 400)]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('O OpenRouter rejeitou o pedido (HTTP 400).')
            ->assertDontSee('O serviço OpenRouter está indisponível (HTTP 400).');
        Http::assertSentCount(2);
        $this->assertSame(0, AiActionLog::count());
    }

    public function test_truncation_after_compatibility_retry_requests_a_shorter_report_without_reasoning(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['error' => ['message' => 'Invalid request parameters']], 400)
            ->push(['choices' => [['finish_reason' => 'length', 'message' => ['content' => '']]]])
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $this->exampleReport()]]]])]);

        $this->assertSame($this->exampleReport(), app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(3);
        $requests = Http::recorded();
        $this->assertArrayNotHasKey('reasoning', $requests[1][0]->data());
        $this->assertSame(['enabled' => false], $requests[2][0]['reasoning']);
        $this->assertStringContainsString('300 a 400 palavras', $requests[2][0]['messages'][1]['content']);
    }

    public function test_http_400_after_truncation_does_not_exceed_retry_limit(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['error' => ['message' => 'Invalid request parameters']], 400)
            ->push(['choices' => [['finish_reason' => 'length', 'message' => ['content' => '']]]])
            ->push(['error' => ['message' => 'Invalid request parameters']], 400)]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('O OpenRouter rejeitou o pedido (HTTP 400).');
        Http::assertSentCount(3);
    }

    public function test_daily_ai_quota_shows_used_and_remaining_requests_and_refreshes(): void
    {
        Http::fake(['openrouter.ai/api/v1/key' => Http::sequence()
            ->push(['data' => ['free_model_daily_requests' => ['used' => 25, 'limit' => 50, 'remaining' => 25]]])
            ->push(['data' => ['free_model_daily_requests' => ['used' => 50, 'limit' => 50, 'remaining' => 0]]])]);

        Livewire::test(AiInsights::class)
            ->assertSee('A consultar...')
            ->call('refreshAiQuota')
            ->assertSet('aiQuota.used', 25)
            ->assertSet('aiQuota.remaining', 25)
            ->assertSet('aiQuota.limit', 50)
            ->assertSee('utilizados')
            ->assertSee('restantes')
            ->assertSee('partilhados com outras funções')
            ->assertSee('Um relatório pode consumir mais de um pedido.')
            ->call('refreshAiQuota')
            ->assertSet('aiQuota.used', 50)
            ->assertSet('aiQuota.remaining', 0)
            ->assertSee('Renova às 00:00 UTC');
        Http::assertSentCount(2);
    }

    public function test_daily_ai_quota_reports_unavailability_without_inventing_values(): void
    {
        Http::fake(['openrouter.ai/api/v1/key' => Http::response([], 503)]);

        Livewire::test(AiInsights::class)
            ->call('refreshAiQuota')
            ->assertSet('aiQuota.available', false)
            ->assertSee('Quota indisponível.')
            ->assertSee('Não foi possível consultar a quota no OpenRouter.')
            ->assertDontSee('restantes');
    }

    public function test_report_uses_current_records_for_its_short_run_rate_forecast(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 12:00:00'));
        foreach (['2026-11-01', '2026-11-06', '2026-11-12', '2026-11-30'] as $date) {
            Expense::create([
                'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
                'description' => 'Despesa', 'amount' => 10, 'spent_at' => $date,
            ]);
        }
        foreach (['2026-11-12', '2026-11-20'] as $date) {
            Income::create([
                'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
                'description' => 'Receita', 'amount' => 40, 'received_at' => $date,
            ]);
        }
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => $this->exampleReport()]]],
        ])]);

        app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        $request = Http::recorded()->first()[0];
        preg_match('/ANÁLISE DETERMINÍSTICA DO BACKEND\n(.*?)\n\nMEMÓRIA/s', $request['messages'][0]['content'], $matches);
        $evidence = json_decode($matches[1] ?? '', true);
        $this->assertSame(30, data_get($evidence, 'recorded_expenses_through_today'));
        $this->assertSame(40, data_get($evidence, 'recorded_income_through_today'));
        $this->assertSame(10, data_get($evidence, 'future_dated_expenses'));
        $this->assertSame(75, data_get($evidence, 'forecast.projected_month_end_expenses'));
        $this->assertArrayNotHasKey('alternative_month_end_expenses', $evidence['forecast']);
        $this->assertArrayNotHasKey('alternative_saving', $evidence['forecast']);
        $this->assertStringContainsString('dá a tua opinião fundamentada', $request['messages'][1]['content']);
        $this->assertStringContainsString('não te concentres apenas nas despesas', $request['messages'][1]['content']);
        $this->assertStringContainsString('500 a 700 palavras', $request['messages'][1]['content']);
        $this->assertStringContainsString('dois a quatro pontos e desenvolve cada um em duas ou três frases', $request['messages'][1]['content']);
        $this->assertSame(4000, $request['max_tokens']);
        $this->assertStringContainsString('**Resultados**, **O que penso**, **Cenários**, **Pontos fortes**, **Pontos negativos**, **O que aconselho** e **Resumo**', $request['messages'][1]['content']);
        $this->assertStringContainsString('não os somes', $request['messages'][1]['content']);
        $this->assertStringContainsString('despesa pontual', $request['messages'][1]['content']);
        $this->assertStringContainsString('sem cortes arbitrários', $request['messages'][1]['content']);
    }

    public function test_report_does_not_project_expenses_without_enough_records(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 12:00:00'));
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => $this->exampleReport()]]],
        ])]);

        app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        Http::assertSent(fn (Request $request) => str_contains(
            $request['messages'][0]['content'],
            '"projected_month_end_expenses":null',
        ) && ! str_contains($request['messages'][0]['content'], '"alternative_month_end_expenses"'));
    }

    public function test_no_alternative_scenario_is_provided_when_the_month_has_ended(): void
    {
        $this->travelTo(Carbon::parse('2026-11-30 20:00:00'));
        foreach (['2026-11-01', '2026-11-15', '2026-11-30'] as $date) {
            Expense::create([
                'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
                'description' => 'Despesa', 'amount' => 10, 'spent_at' => $date,
            ]);
        }
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => $this->exampleReport()]]],
        ])]);

        app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        Http::assertSent(fn (Request $request) => str_contains($request['messages'][0]['content'], '"projected_month_end_expenses":30')
            && ! str_contains($request['messages'][0]['content'], '"alternative_month_end_expenses"'));
    }

    public function test_company_paid_subscriptions_are_excluded_from_personal_costs(): void
    {
        $category = Category::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'name' => 'Assinaturas', 'slug' => 'assinaturas',
        ]);
        $common = [
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'category_id' => $category->id, 'billing_day' => 1, 'is_active' => true,
        ];
        Subscription::create($common + ['name' => 'Empresa', 'amount' => 40, 'cycle' => 'monthly', 'paid_by_company' => true]);
        $companyOnly = app(FinancialIntelligenceService::class)->snapshot($this->workspace);
        $this->assertSame(0, $companyOnly['active_subscriptions']);
        $this->assertSame(0.0, $companyOnly['active_subscription_cost']);

        Subscription::create($common + ['name' => 'Pessoal', 'amount' => 120, 'cycle' => 'annual', 'paid_by_company' => false]);
        Subscription::create(array_merge($common, ['name' => 'Inativa', 'amount' => 90, 'cycle' => 'monthly', 'paid_by_company' => false, 'is_active' => false]));

        $snapshot = app(FinancialIntelligenceService::class)->snapshot($this->workspace);
        $this->assertSame(1, $snapshot['active_subscriptions']);
        $this->assertSame(10.0, $snapshot['active_subscription_cost']);

        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => $this->exampleReport()]]],
        ])]);
        app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);
        Http::assertSent(fn (Request $request) => str_contains($request['messages'][0]['content'], '"active_personal_subscription_cost":10')
            && str_contains($request['messages'][1]['content'], 'não atribuas pagador a nenhuma assinatura individual sem prova'));

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Diagnóstico')
            ->assertDontSee('As subscrições marcadas como pagas pela empresa não contam para o custo pessoal.')
            ->assertDontSee('A IA pode cometer erros; confirma os valores e conclusões antes de tomar decisões.');
    }

    public function test_truncated_report_is_retried_without_reusing_the_incomplete_text(): void
    {
        Log::spy();
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push([
                'choices' => [['finish_reason' => 'length', 'message' => ['content' => 'Relatório incompleto']]],
                'usage' => ['total_tokens' => 100],
            ])
            ->push([
                'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $this->exampleReport()]]],
                'usage' => ['total_tokens' => 80],
            ])]);

        $result = app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        $this->assertSame($this->exampleReport(), $result['content']);
        $this->assertSame(180, $result['tokens']);
        Http::assertSentCount(2);
        $requests = Http::recorded();
        $this->assertSame($requests[0][0]['messages'][0], $requests[1][0]['messages'][0]);
        $this->assertStringNotContainsString('Relatório incompleto', json_encode($requests[1][0]['messages']));
        $this->assertStringContainsString('300 a 400 palavras', $requests[1][0]['messages'][1]['content']);
        $this->assertSame(['enabled' => false], $requests[0][0]['reasoning']);
        $this->assertSame('openrouter/free', $requests[1][0]['model']);
        Log::shouldHaveReceived('warning')->with('CFO report invalid or incomplete', \Mockery::on(
            fn (array $context) => $context['validation_reason'] === 'truncated'
                && ! array_key_exists('content', $context)
        ))->once();
    }

    public function test_empty_report_is_retried_and_text_blocks_are_supported(): void
    {
        $parts = explode("\n\n", $this->exampleReport(), 2);
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'length', 'message' => ['content' => null]]]])
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => [
                ['type' => 'text', 'text' => $parts[0]],
                ['type' => 'text', 'text' => $parts[1]],
            ]]]]])]);

        $result = app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        $this->assertSame($this->exampleReport(), $result['content']);
        Http::assertSentCount(2);
    }

    public function test_garbled_success_response_is_retried_with_a_different_model(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => [
                'content' => '-実際 قم Depend Factorsbedingungen Target expressions, Expansion (健的、 suas光 WWạn俄ISH sindATION EnIn Ex arerette興textlø',
            ]]]])
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $this->exampleReport()]]]])]);

        $result = app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        $this->assertSame($this->exampleReport(), $result['content']);
        Http::assertSentCount(2);
        $requests = Http::recorded();
        $this->assertSame('google/gemma-4-31b-it:free', $requests[0][0]['model']);
        $this->assertSame('openrouter/free', $requests[1][0]['model']);
    }

    public function test_report_without_required_sections_is_not_cached_as_success(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => str_repeat('texto sem sentido ', 50)]]],
        ])]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('O relatório não passou nas verificações de formato ou valores')
            ->assertDontSee('texto sem sentido');
        Http::assertSentCount(2);
        $this->assertSame(0, AiActionLog::count());
    }

    public function test_empty_final_response_is_explained_and_previous_report_is_preserved(): void
    {
        Log::spy();
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => str_repeat('texto ', 50)]]]])
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => null]]]])]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('A última resposta da IA veio vazia.');
        Http::assertSentCount(2);
        Log::shouldHaveReceived('warning')->with('CFO report invalid or incomplete', \Mockery::on(
            fn (array $context) => $context['validation_reason'] === 'missing_or_empty_sections'
                && ! array_key_exists('content', $context)
        ))->once();
        Log::shouldHaveReceived('warning')->with('CFO report invalid or incomplete', \Mockery::on(
            fn (array $context) => $context['validation_reason'] === 'empty'
                && ! array_key_exists('content', $context)
        ))->once();
    }

    public function test_report_retries_missing_topics_and_reorders_complete_topics(): void
    {
        $report = $this->exampleReport();
        $missing = str_replace("**Cenários**\n- A evolução depende dos rendimentos e dos compromissos ainda por confirmar.\n\n", '', $report);
        $strong = "**Pontos fortes**\n- Os investimentos registados permitem acompanhar a evolução da carteira.";
        $negative = "**Pontos negativos**\n- A falta de dados confirmados limita a avaliação dos riscos.";
        $outOfOrder = str_replace(
            $strong."\n\n".$negative,
            $negative."\n\n".$strong,
            $report,
        );
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $missing]]]])
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $outOfOrder]]]])]);

        $this->assertSame($report, app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(2);
    }

    public function test_complete_report_is_not_discarded_for_exceeding_the_target_length(): void
    {
        $report = $this->exampleReport();
        preg_match_all('/[\p{L}\p{N}]+(?:[.,\'’_-][\p{L}\p{N}]+)*/u', $report, $words);
        $atLimit = $report.' '.str_repeat('palavra ', 700 - count($words[0]));
        $overLimit = $atLimit.' palavra';
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $overLimit]]],
        ])]);

        $this->assertSame(trim($overLimit), app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_complete_report_paragraphs_are_converted_to_points(): void
    {
        $withoutBullets = str_replace('- ', '', $this->exampleReport());
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $withoutBullets]]],
        ])]);

        $this->assertSame($this->exampleReport(), app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_report_accepts_inline_markdown_headings_and_equivalent_topic_names(): void
    {
        $report = $this->exampleReport();
        $variant = str_replace(
            [
                "**Resultados**\n- Os registos disponíveis permitem uma análise inicial prudente.",
                "**Cenários**\n- A evolução",
                '**Pontos negativos**',
                "**O que aconselho**\n- Confirma",
            ],
            [
                "**Resultados:** Os registos disponíveis permitem uma análise inicial prudente.",
                "### Cenários: A evolução",
                '5. Pontos fracos',
                "* **O que ele aconselha:** Confirma",
            ],
            $report,
        );
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $variant]]],
        ])]);

        $this->assertSame($report, app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_report_normalizes_the_previous_advice_heading(): void
    {
        $report = $this->exampleReport();
        $oldHeading = str_replace('**O que aconselho**', '**O que aconselha**', $report);
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $oldHeading]]],
        ])]);

        $this->assertSame($report, app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_inline_heading_preserves_bold_text_in_the_section_body(): void
    {
        $report = str_replace(
            'Os registos disponíveis permitem',
            '**Os registos** disponíveis permitem',
            $this->exampleReport(),
        );
        $variant = str_replace(
            "**Resultados**\n- **Os registos**",
            'Resultados: **Os registos**',
            $report,
        );
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $variant]]],
        ])]);

        $this->assertSame($report, app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_report_accepts_four_points_in_a_section(): void
    {
        $report = str_replace(
            "**O que penso**\n",
            "**O que penso**\n- Confirma as prioridades antes de tomar decisões financeiras.\n- Avalia os riscos em função dos teus objetivos.\n",
            $this->exampleReport(),
        );
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $report]]],
        ])]);

        $this->assertSame($report, app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_unsupported_scenario_amount_and_percentage_are_retried(): void
    {
        Log::spy();
        $invalidPercent = $this->exampleReport()."\nReduzir uma despesa em 50%.";
        $invalidAmount = $this->exampleReport()."\nUma despesa hipotética de 200 EUR.";
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $invalidPercent]]]])
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $invalidAmount]]]])]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('A última resposta da IA apresentou valores que não foi possível confirmar.')
            ->assertDontSee('200 EUR');
        Http::assertSentCount(2);
        $this->assertSame(0, AiActionLog::count());
        Log::shouldHaveReceived('warning')->with('CFO report invalid or incomplete', \Mockery::on(
            fn (array $context) => $context['validation_reason'] === 'unsupported_values'
                && ! array_key_exists('content', $context)
        ))->twice();
    }

    public function test_unsupported_values_trigger_a_qualitative_retry(): void
    {
        $invalid = $this->exampleReport()."\n- Uma despesa hipotética de 200 EUR.";
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $invalid]]]])
            ->push(['choices' => [['finish_reason' => 'stop', 'message' => ['content' => $this->exampleReport()]]]])]);

        $result = app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        $this->assertStringContainsString('esta análise privilegiou uma abordagem qualitativa', $result['content']);
        $this->assertStringContainsString($this->exampleReport(), $result['content']);
        Http::assertSentCount(2);
        $requests = Http::recorded();
        $this->assertStringContainsString('Não escrevas montantes monetários nem percentagens', $requests[1][0]['messages'][1]['content']);
        $this->assertStringContainsString('não apresentes montantes monetários nem percentagens', $requests[1][0]['messages'][0]['content']);
        $this->assertStringNotContainsString('usa os números reais disponíveis', $requests[1][0]['messages'][0]['content']);
        $this->assertStringNotContainsString('Indica valores monetários e percentagens', $requests[1][0]['messages'][1]['content']);
        $this->assertStringNotContainsString('Uma despesa hipotética de 200 EUR', json_encode($requests[1][0]['messages']));
    }

    public function test_supported_currency_and_percent_values_pass_the_check(): void
    {
        Expense::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'description' => 'Despesa', 'amount' => 1234.56, 'spent_at' => now()->toDateString(),
        ]);
        Investment::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'name' => 'Investimento', 'type' => 'ETF', 'quantity' => 1,
            'average_price' => 100, 'current_price' => 125,
        ]);
        $report = $this->exampleReport()."\n- Total registado: 1.234,56 EUR. Valorização: 25%. Valor: EUR 1.234,56.\n- Valor e valorização: 1.234,56 EUR 25%.";
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $report]]],
        ])]);

        $this->assertSame($report, app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_currency_prefix_after_another_amount_is_still_checked(): void
    {
        Expense::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'description' => 'Despesa', 'amount' => 1234.56, 'spent_at' => now()->toDateString(),
        ]);
        Investment::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'name' => 'Investimento', 'type' => 'ETF', 'quantity' => 1,
            'average_price' => 100, 'current_price' => 125,
        ]);
        $report = $this->exampleReport()."\n- Valor e valorização: 1.234,56 EUR 25%. Outra despesa: EUR 200.";
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $report]]],
        ])]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('valores que não foi possível confirmar');
        Http::assertSentCount(2);
    }

    public function test_supported_whole_currency_amount_with_thousands_separator_passes(): void
    {
        Expense::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'description' => 'Despesa', 'amount' => 1234, 'spent_at' => now()->toDateString(),
        ]);
        $report = $this->exampleReport()."\n- Total registado: 1.234 EUR.";
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'stop', 'message' => ['content' => $report]]],
        ])]);

        $this->assertSame($report, app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace)['content']);
        Http::assertSentCount(1);
    }

    public function test_personal_report_does_not_use_stale_financial_memories(): void
    {
        AiMemory::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'type' => 'financial_context', 'key' => 'old', 'value' => ['old_amount' => 98765],
            'importance' => 80, 'is_active' => true,
        ]);
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => $this->exampleReport()]]],
        ])]);

        app(AiBrainService::class)->generateExecutiveReport($this->user, $this->workspace);

        Http::assertSent(fn (Request $request) => str_contains($request['messages'][0]['content'], "MEMÓRIA CONTROLADA DO WORKSPACE ATUAL\n[]")
            && ! str_contains($request['messages'][0]['content'], '98765'));
    }

    public function test_cached_personal_report_displays_the_new_advice_heading(): void
    {
        foreach (['**O que aconselha**' => '**O que aconselho**', '### O que aconselha' => '### O que aconselho'] as $old => $new) {
            $cachedReport = str_replace('**O que aconselho**', $old, $this->exampleReport());
            Cache::put('ai-insights:'.$this->user->id.':workspace:'.$this->workspace->id, [
                'text' => $cachedReport, 'at' => now()->toIso8601String(), 'numeric_check_version' => 1,
            ]);

            Livewire::test(AiInsights::class)
                ->assertSet('aiAnalysis', str_replace($old, $new, $cachedReport))
                ->assertSeeHtml('<h3>O que aconselho</h3>')
                ->assertDontSeeHtml('<h3>O que aconselha</h3>');
        }
    }

    public function test_old_cached_report_is_marked_for_review_until_replaced(): void
    {
        Cache::put('ai-insights:'.$this->user->id.':workspace:'.$this->workspace->id, [
            'text' => 'Relatório anterior', 'at' => now()->toIso8601String(),
        ]);
        Mail::fake();
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['message' => ['content' => $this->exampleReport()]]],
        ])]);

        Livewire::test(AiInsights::class)
            ->assertSee('Este relatório foi gerado antes da verificação de valores')
            ->call('generateInsights')
            ->assertDontSee('Este relatório foi gerado antes da verificação de valores')
            ->assertSee('Os registos disponíveis permitem uma análise inicial prudente.');
        Livewire::test(AiInsights::class)->assertSet('reportNeedsReview', false);
    }

    public function test_obviously_corrupted_cached_report_is_not_shown(): void
    {
        Cache::put('ai-insights:'.$this->user->id.':workspace:'.$this->workspace->id, [
            'text' => '-実際 قم Depend Factorsbedingungen Target expressions, Expansion (健的、 suas光 WWạn俄ISH sindATION EnIn Ex arerette興textlø',
            'at' => now()->toIso8601String(),
        ]);

        Livewire::test(AiInsights::class)
            ->assertSet('aiAnalysis', '')
            ->assertSee('O diagnóstico guardado está ilegível.')
            ->assertDontSee('Factorsbedingungen');
    }

    public function test_incomplete_report_is_not_presented_as_success_after_retry(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [['finish_reason' => 'length', 'message' => ['content' => 'Relatório incompleto']]],
        ])]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSee('A última resposta da IA foi cortada e o relatório ficou incompleto.')
            ->assertDontSee('Relatório incompleto')
            ->assertSet('aiAnalysis', 'Relatório anterior');
        Http::assertSentCount(2);
        $this->assertSame(0, AiActionLog::count());
    }

    public function test_provider_failure_on_retry_preserves_previous_report(): void
    {
        Http::fake(['openrouter.ai/api/v1/chat/completions' => Http::sequence()
            ->push(['choices' => [['finish_reason' => 'length', 'message' => ['content' => 'Relatório incompleto']]]])
            ->push([], 429)]);

        Livewire::test(AiInsights::class)
            ->set('aiAnalysis', 'Relatório anterior')
            ->call('generateInsights')
            ->assertSet('aiAnalysis', 'Relatório anterior')
            ->assertSee('O limite de pedidos foi atingido')
            ->assertDontSee('Relatório incompleto');
        Http::assertSentCount(2);
        $this->assertSame(0, AiActionLog::count());
    }

    public function test_control_center_explains_when_a_cached_report_has_no_extractable_topics(): void
    {
        Cache::put('ai-insights:'.$this->user->id.':workspace:'.$this->workspace->id, [
            'text' => 'Relatório de teste', 'at' => now()->toIso8601String(),
        ]);

        Livewire::test(AiControlCenter::class)
            ->assertDontSee('Último diagnóstico CFO')
            ->assertDontSee('Action Center')
            ->assertDontSee('Relatório de teste')
            ->assertSee('Insight Center')
            ->assertDontSee('Gerir memória da IA')
            ->assertSee('Não foi possível extrair estes tópicos do relatório guardado.')
            ->assertSee('Privacidade e dados guardados')
            ->assertDontSee('Memória ativa');

        Livewire::test(AiInsights::class)->assertSee('Relatório de teste');
    }

    public function test_report_highlights_use_the_current_cached_analysis_and_refresh_without_ai_requests(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)->shouldReceive('analyze')->andReturn([]);
        Http::fake();
        $key = 'ai-insights:'.$this->user->id.':workspace:'.$this->workspace->id;
        Cache::put($key, ['text' => $this->exampleReport(), 'at' => now()->toIso8601String(), 'numeric_check_version' => 1]);

        $center = Livewire::test(AiControlCenter::class)
            ->assertSee('Destaques do diagnóstico')
            ->assertSee('As valorizações registadas são positivas')
            ->assertSee('A evolução depende dos rendimentos')
            ->assertSee('Confirma os rendimentos recebidos')
            ->assertDontSee('Os registos disponíveis permitem uma análise inicial prudente.')
            ->assertSeeHtml('href="#diagnostico-financeiro"');

        $updated = str_replace('As valorizações registadas são positivas', 'Nova opinião fundamentada', $this->exampleReport());
        Cache::put($key, ['text' => $updated, 'at' => now()->toIso8601String(), 'numeric_check_version' => 1]);
        $center->dispatch('cfo-report-generated')
            ->assertSee('Nova opinião fundamentada')
            ->assertDontSee('As valorizações registadas são positivas');
        Http::assertNothingSent();
    }

    public function test_report_highlights_do_not_use_other_workspaces_or_unreadable_reports(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)->shouldReceive('analyze')->andReturn([]);
        $otherWorkspaceId = $this->workspace->id + 1000;
        Cache::put('ai-insights:'.$this->user->id.':workspace:'.$otherWorkspaceId, ['text' => $this->exampleReport()]);

        Livewire::test(AiControlCenter::class)
            ->assertSee('Ainda não existe um diagnóstico guardado.')
            ->assertDontSee('As valorizações registadas são positivas');

        Cache::put('ai-insights:'.$this->user->id.':workspace:'.$this->workspace->id, [
            'text' => '-実際 قم Depend Factorsbedingungen Target expressions, Expansion (健的、 suas光 WWạn俄ISH sindATION EnIn Ex arerette興textlø',
        ]);
        Livewire::test(AiControlCenter::class)
            ->assertSee('Não foi possível extrair estes tópicos')
            ->assertDontSee('Factorsbedingungen');
    }

    public function test_old_report_highlights_display_the_existing_verification_warning(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)->shouldReceive('analyze')->andReturn([]);
        Cache::put('ai-insights:'.$this->user->id.':workspace:'.$this->workspace->id, ['text' => $this->exampleReport()]);

        Livewire::test(AiControlCenter::class)
            ->assertSee('As valorizações registadas são positivas')
            ->assertSee('Este relatório foi gerado antes da verificação de valores.');
    }

    public function test_memory_controls_remain_available_in_profile_and_are_user_scoped(): void
    {
        $otherUser = User::factory()->create();
        $memory = AiMemory::create([
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'type' => 'financial_context', 'key' => 'teste', 'value' => ['contexto' => 'valor de teste'],
            'importance' => 80, 'is_active' => true,
        ]);
        $otherMemory = AiMemory::create([
            'user_id' => $otherUser->id, 'workspace_id' => null,
            'type' => 'financial_context', 'key' => 'privado', 'value' => ['contexto' => 'outro utilizador'],
            'importance' => 80, 'is_active' => true,
        ]);

        $this->get(route('profile.edit'))->assertOk()->assertSee('Dados guardados pela IA');
        Livewire::test(AiMemorySettings::class)
            ->assertSee('valor de teste')
            ->assertDontSee('outro utilizador')
            ->call('forgetMemory', $otherMemory->id)
            ->assertSet('memoryError', 'Não foi possível encontrar esta memória.')
            ->call('clearMemories')
            ->assertSet('memoryError', null)
            ->assertDontSee('valor de teste');
        $this->assertDatabaseMissing('ai_memories', ['id' => $memory->id]);
        $this->assertDatabaseHas('ai_memories', ['id' => $otherMemory->id]);
    }

    public function test_insight_filters_and_actions_remain_scoped_and_interactive(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)
            ->shouldReceive('analyze')->andReturn([]);
        $common = [
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'type' => 'risk', 'message' => 'Detalhes do alerta', 'source' => 'observer',
            'confidence' => 80, 'score' => 70,
        ];
        $unread = AiInsight::create($common + ['title' => 'Alerta por ler', 'priority' => 'high', 'category' => 'spending']);
        $read = AiInsight::create($common + ['title' => 'Alerta lido', 'priority' => 'medium', 'category' => 'revenue', 'read_at' => now()]);
        $otherUser = User::factory()->create();
        $other = AiInsight::create(array_merge($common, [
            'user_id' => $otherUser->id, 'title' => 'Alerta de outro utilizador', 'priority' => 'high', 'category' => 'spending',
        ]));

        $center = Livewire::test(AiControlCenter::class)
            ->assertSee('Alerta por ler')
            ->assertSee('Alerta lido')
            ->assertDontSee('Alerta de outro utilizador')
            ->set('insightFilter', 'unread')
            ->assertSee('Alerta por ler')
            ->assertDontSee('Alerta lido')
            ->set('insightCategory', 'revenue')
            ->assertSee('Nenhum alerta corresponde aos filtros')
            ->call('resetInsightFilters')
            ->set('insightFilter', 'important')
            ->assertSee('Alerta por ler')
            ->assertDontSee('Alerta lido')
            ->call('feedback', $unread->id, 'useful')
            ->assertDispatched('toast', variant: 'success', text: 'Obrigado pelo teu feedback.')
            ->assertSee('Avaliação guardada: consideraste este alerta útil.');
        $this->assertSame('useful', $unread->fresh()->data['feedback']);
        $center->call('feedback', $unread->id, 'not_useful')
            ->assertSee('Avaliação guardada: consideraste este alerta não útil.')
            ->call('feedback', $unread->id, 'more')
            ->assertSee('Avaliação guardada: queres mais alertas deste tipo.')
            ->assertSee('Esta escolha ainda não altera automaticamente os alertas futuros.');
        $this->assertSame('more', $unread->fresh()->data['feedback']);
        $center->call('markRead', $unread->id)
            ->assertDispatched('toast', variant: 'success', text: 'Alerta marcado como lido.')
            ->set('insightFilter', 'unread')
            ->assertDontSee('Alerta por ler')
            ->call('resetInsightFilters')
            ->call('dismiss', $read->id)
            ->assertDispatched('toast', variant: 'success', text: 'Alerta dispensado.')
            ->assertDontSee('Alerta lido')
            ->call('dismiss', $other->id)
            ->assertSet('insightActionError', 'O alerta já não está disponível.')
            ->assertSee('O alerta já não está disponível.');
        $this->assertNotNull($unread->fresh()->read_at);
        $this->assertNotNull($read->fresh()->dismissed_at);
        $this->assertNull($other->fresh()->dismissed_at);
    }

    public function test_feedback_rejects_invalid_values_and_unavailable_insights(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)->shouldReceive('analyze')->andReturn([]);
        $common = [
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'type' => 'risk', 'message' => 'Detalhes do alerta', 'source' => 'observer',
            'confidence' => 80, 'score' => 70, 'priority' => 'high', 'category' => 'spending',
        ];
        $insight = AiInsight::create($common + ['title' => 'Alerta']);
        $other = AiInsight::create(array_merge($common, [
            'user_id' => User::factory()->create()->id, 'title' => 'Alerta privado',
        ]));
        $dismissed = AiInsight::create($common + ['title' => 'Alerta dispensado', 'dismissed_at' => now()]);

        Livewire::test(AiControlCenter::class)
            ->call('feedback', $insight->id, 'invalid')
            ->assertHasErrors(['feedback']);
        Livewire::test(AiControlCenter::class)
            ->call('feedback', $other->id, 'useful')
            ->assertSet('insightActionError', 'O alerta já não está disponível.')
            ->call('feedback', $dismissed->id, 'more')
            ->assertSet('insightActionError', 'O alerta já não está disponível.')
            ->call('feedback', $insight->id, 'useful')
            ->assertSet('insightActionError', null);

        $this->assertArrayNotHasKey('feedback', $other->fresh()->data ?? []);
        $this->assertArrayNotHasKey('feedback', $dismissed->fresh()->data ?? []);
        $this->assertSame('useful', $insight->fresh()->data['feedback']);
    }

    public function test_health_widget_explains_score_components_and_uses_its_recorded_income_savings_rate(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)->shouldReceive('analyze')->andReturn([]);
        $this->mock(\App\Services\AI\FinancialHealthScoreService::class)->shouldReceive('personal')->andReturn([
            'score' => 65,
            'label' => 'Bom',
            'savings_rate' => 92.6,
            'inputs' => ['breakdown' => [
                'savings' => ['label' => 'Taxa de Poupança', 'weight' => '30%', 'score' => 90, 'available' => true, 'value' => 45, 'unit' => '%'],
                'budget' => ['label' => 'Disciplina Orçamental', 'weight' => '20%', 'score' => null, 'available' => false, 'reason' => 'Não existem limites orçamentais configurados.'],
            ]],
        ]);

        Livewire::test(AiControlCenter::class)
            ->assertSee('Ver cálculo')
            ->assertSee('Poupança sobre receitas registadas:')
            ->assertSee('45,0%')
            ->assertDontSee('92.6%')
            ->assertSee('1 de 2 componentes com dados')
            ->assertSee('90/100')
            ->assertSee('Não existem limites orçamentais configurados.');
    }

    public function test_unread_widget_filters_insights_and_action_details_remain_user_scoped(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)->shouldReceive('analyze')->andReturn([]);
        $common = [
            'user_id' => $this->user->id, 'workspace_id' => $this->workspace->id,
            'type' => 'risk', 'message' => 'Detalhes', 'source' => 'observer', 'confidence' => 80, 'score' => 70,
        ];
        AiInsight::create($common + ['title' => 'Alerta importante por ler', 'priority' => 'high', 'category' => 'spending']);
        AiInsight::create($common + ['title' => 'Alerta importante lido', 'priority' => 'critical', 'category' => 'spending', 'read_at' => now()]);
        $otherUser = User::factory()->create();
        $action = ['workspace_id' => $this->workspace->id, 'action_type' => 'analysis', 'tool_name' => 'generate_executive_report'];
        AiActionLog::create($action + ['user_id' => $this->user->id, 'status' => 'completed', 'result_payload' => ['title' => 'Diagnóstico gerado']]);
        AiActionLog::create($action + ['user_id' => $this->user->id, 'status' => 'awaiting_confirmation', 'result_payload' => ['title' => 'Ação para confirmar']]);
        AiActionLog::create($action + ['user_id' => $otherUser->id, 'status' => 'failed', 'result_payload' => ['title' => 'Ação privada']]);

        Livewire::test(AiControlCenter::class)
            ->assertSee('1 por ler com prioridade alta ou crítica')
            ->assertSee('1 concluídas · 1 pendentes')
            ->assertSee('Diagnóstico gerado')
            ->assertSee('A aguardar confirmação')
            ->assertDontSee('Ação privada')
            ->set('insightCategory', 'revenue')
            ->call('showUnreadInsights')
            ->assertSet('insightFilter', 'unread')
            ->assertSet('insightCategory', 'all')
            ->assertSee('Alerta importante por ler')
            ->assertDontSee('Alerta importante lido');
    }

    public function test_widgets_explain_when_no_personal_data_or_actions_are_available(): void
    {
        $this->mock(\App\Services\AI\ProactiveAiService::class)->shouldReceive('analyze')->andReturn([]);

        Livewire::test(AiControlCenter::class)
            ->assertSee('Sem dados suficientes')
            ->assertSee('Poupança indisponível: sem receitas registadas no período.')
            ->assertSee('0 de 5 componentes com dados')
            ->assertSee('Ainda não existem operações registadas neste workspace.')
            ->call('showUnreadInsights')
            ->assertSet('insightFilter', 'unread');
    }
}
