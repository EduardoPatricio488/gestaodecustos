<?php

namespace App\Providers;

use App\Listeners\CreateDefaultCategories;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            CreateDefaultCategories::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
