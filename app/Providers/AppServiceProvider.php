<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event; // <-- ADICIONAR
use Illuminate\Auth\Events\Login;      // <-- ADICIONAR
use App\Listeners\UpdateLastLogin;
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
    $this->configureDefaults();
    $this->registerViewNamespaces();
Event::listen(Login::class, UpdateLastLogin::class);
    \Illuminate\Support\Facades\App::setLocale('pt');
    \Illuminate\Support\Carbon::setLocale('pt');

    // FORÇAR HTTPS SEMPRE QUE ESTIVER NO NGROK
    if (str_contains(request()->getHost(), 'ngrok-free.app') || str_contains(request()->getHost(), 'ngrok-free.dev')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
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
