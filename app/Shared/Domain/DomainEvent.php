<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use DateTimeImmutable;

abstract class DomainEvent
{
    private string $eventId;
    private string $aggregateId;
    private string $occurredOn;
    
    public function __construct(string $aggregateId, ?string $eventId = null, ?string $occurredOn = null)
    {
        $this->aggregateId = $aggregateId;
        $this->eventId = $eventId ?: uniqid();
        $this->occurredOn = $occurredOn ?: (new DateTimeImmutable())->format('Y-m-d H:i:s');
    }
    
    abstract public function eventName(): string;
    
    public function aggregateId(): string
    {
        return $this->aggregateId;
    }
    
    public function eventId(): string
    {
        return $this->eventId;
    }
    
    public function occurredOn(): string
    {
        return $this->occurredOn;
    }
    
    abstract public function toPrimitives(): array;
    
    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;
}
