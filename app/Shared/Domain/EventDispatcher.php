<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface EventDispatcher
{
    /**
     * Dispatches an array of DomainEvent events.
     *
     * @param DomainEvent[] $events
     */
    public function dispatch(array $events): void;
}