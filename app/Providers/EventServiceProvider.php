<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Shared\Domain\EventDispatcher;
use App\Shared\Infrastructure\SimpleEventDispatcher;
use App\Ledger\Account\Application\Listeners\LogAccountCreated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    public function register(): void
    {
        $this->app->singleton(EventDispatcher::class, function () {
            $dispatcher = new SimpleEventDispatcher();

            // تسجيل المستمع لحدث account.created
            $dispatcher->addListener('account.created', new LogAccountCreated());

            return $dispatcher;
        });
    }
}
