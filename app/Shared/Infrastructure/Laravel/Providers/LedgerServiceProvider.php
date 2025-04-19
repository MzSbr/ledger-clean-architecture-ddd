<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel\Providers;

use App\Shared\Infrastructure\Laravel\Providers\BusServiceProvider;
use App\Shared\Infrastructure\Laravel\Providers\ExceptionServiceProvider;
use App\Shared\Infrastructure\Laravel\Providers\RepositoryServiceProvider;
use Illuminate\Support\ServiceProvider;

final class LedgerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(BusServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(ExceptionServiceProvider::class);
    }
    
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../../../../database/migrations');
    }
}
