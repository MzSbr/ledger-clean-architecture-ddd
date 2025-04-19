<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Laravel;

use App\Shared\Application\Query;
use App\Shared\Application\QueryBus;
use Illuminate\Container\Container;
use Throwable;

final class LaravelQueryBus implements QueryBus
{
    public function __construct(
        private readonly Container $container
    ) {
    }

    public function ask(Query $query): mixed
    {
        $handlerClass = $this->getHandlerClass($query);
        $handler = $this->container->make($handlerClass);
        
        try {
            return $handler($query);
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    private function getHandlerClass(Query $query): string
    {
        $queryClass = get_class($query);
        $handlerClass = str_replace('Query', 'Handler', $queryClass);
        
        if (!class_exists($handlerClass)) {
            throw new \RuntimeException("Query handler for {$queryClass} not found");
        }
        
        return $handlerClass;
    }
}
