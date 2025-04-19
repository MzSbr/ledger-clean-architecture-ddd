<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\DomainEvent;
use App\Shared\Domain\EventDispatcher;

final class SimpleEventDispatcher implements EventDispatcher
{
    /** 
     * Array of listeners organized by event name.
     * @var array<string, callable[]>
     */
    private array $listeners = [];

    /**
     * Register a listener for a specific event name.
     *
     * @param string   $eventName
     * @param callable $listener
     */
    public function addListener(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * Dispatch an array of DomainEvent events.
     *
     * @param DomainEvent[] $events
     */
    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            if (!$event instanceof DomainEvent) {
                continue;
            }

            $eventName = $event->eventName();
            if (!empty($this->listeners[$eventName])) {
                foreach ($this->listeners[$eventName] as $listener) {
                    $listener($event);
                }
            }
        }
    }
}