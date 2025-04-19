<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel;

use App\Shared\Application\CommandBus;
use App\Shared\Application\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Throwable;

final class LaravelCommandBus implements CommandBus
{
    public function __construct(
        private readonly Container $container
    ) {
    }

    public function dispatch(Command $command): void
    {
        $handlerClass = $this->getHandlerClass($command);
        $handler = $this->container->make($handlerClass);
        
        try {
            DB::transaction(function () use ($handler, $command) {
                $handler($command);
            });
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    private function getHandlerClass(Command $command): string
    {
        $commandClass = get_class($command);
        $handlerClass = str_replace('Command', 'Handler', $commandClass);
        
        if (!class_exists($handlerClass)) {
            throw new \RuntimeException("Command handler for {$commandClass} not found");
        }
        
        return $handlerClass;
    }
}
