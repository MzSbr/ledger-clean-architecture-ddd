<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel\Providers;

use App\Shared\Application\CommandBus;
use App\Shared\Application\QueryBus;
use App\Shared\Infrastructure\Laravel\LaravelCommandBus;
use App\Shared\Infrastructure\Laravel\LaravelQueryBus;
use Illuminate\Support\ServiceProvider;

final class BusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CommandBus::class, LaravelCommandBus::class);
        $this->app->singleton(QueryBus::class, LaravelQueryBus::class);
    }
}
