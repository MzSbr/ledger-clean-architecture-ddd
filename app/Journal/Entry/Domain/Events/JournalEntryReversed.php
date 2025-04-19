<?php

declare(strict_types=1);

namespace App\Journal\Entry\Domain\Events;

use App\Shared\Domain\DomainEvent;

final class JournalEntryReversed extends DomainEvent
{
    private string $reversalId;

    public function __construct(
        string $id,
        string $reversalId,
        string $eventId = null,
        string $occurredOn = null
    ) {
        parent::__construct($id, $eventId, $occurredOn);
        $this->reversalId = $reversalId;
    }

    public function eventName(): string
    {
        return 'journal_entry.reversed';
    }

    public function reversalId(): string
    {
        return $this->reversalId;
    }

    public function toPrimitives(): array
    {
        return [
            'reversalId' => $this->reversalId,
        ];
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self {
        return new self(
            $aggregateId,
            $body['reversalId'],
            $eventId,
            $occurredOn
        );
    }
}
