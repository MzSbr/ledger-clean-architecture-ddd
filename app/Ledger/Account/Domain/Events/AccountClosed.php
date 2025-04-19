<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain\Events;

use App\Shared\Domain\DomainEvent;

final class AccountClosed extends DomainEvent
{
    public function __construct(
        string $id,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($id, $eventId, $occurredOn);
    }

    public function eventName(): string
    {
        return 'account.closed';
    }

    public function toPrimitives(): array
    {
        return [];
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self {
        return new self(
            $aggregateId,
            $eventId,
            $occurredOn
        );
    }
}
