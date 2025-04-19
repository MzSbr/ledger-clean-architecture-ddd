<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel\Providers;

use App\Shared\Infrastructure\Laravel\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;

final class ExceptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ExceptionHandler::class,
            Handler::class
        );
    }
}
