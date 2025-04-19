<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class AggregateRoot
{
    private array $domainEvents = [];
    
    final protected function record(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }
    
    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];
        
        return $domainEvents;
    }
}
