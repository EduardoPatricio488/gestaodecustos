<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Configurações padrão do Starter Kit
        $this->configureDefaults();
        $this->registerViewNamespaces();

        // 2. Configuração de Idioma (Português)
        App::setLocale('pt');
        Carbon::setLocale('pt');

        // 3. FORÇAR HTTPS APENAS PARA NGROK
        // Isto permite que o localhost continue a funcionar em HTTP
        // Mas garante que o Ngrok e o iPhone usem HTTPS para o CSS não quebrar
        if (str_contains(request()->getHost(), 'ngrok')) {
            URL::forceScheme('https');
        }
    }

    protected function registerViewNamespaces(): void
    {
        View::addNamespace('pages', resource_path('views/pages'));

        Blade::anonymousComponentPath(resource_path('views/layouts'), 'layouts');
        Blade::anonymousComponentPath(resource_path('views/pages'), 'pages');
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
