<?php

declare(strict_types=1);

namespace App\Journal\Entry\Domain\Events;

use App\Shared\Domain\DomainEvent;

final class JournalEntryCreated extends DomainEvent
{
    private string $type;
    private string $description;
    private string $date;
    private string $currency;
    private ?string $reversalOf;
    private ?array $extra;

    public function __construct(
        string $id,
        string $type,
        string $description,
        string $date,
        string $currency,
        ?string $reversalOf,
        ?array $extra,
        string $eventId = null,
        string $occurredOn = null
    ) {
        parent::__construct($id, $eventId, $occurredOn);
        $this->type = $type;
        $this->description = $description;
        $this->date = $date;
        $this->currency = $currency;
        $this->reversalOf = $reversalOf;
        $this->extra = $extra;
    }

    public function eventName(): string
    {
        return 'journal_entry.created';
    }

    public function type(): string
    {
        return $this->type;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function date(): string
    {
        return $this->date;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function reversalOf(): ?string
    {
        return $this->reversalOf;
    }

    public function extra(): ?array
    {
        return $this->extra;
    }

    public function toPrimitives(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
            'date' => $this->date,
            'currency' => $this->currency,
            'reversalOf' => $this->reversalOf,
            'extra' => $this->extra,
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
            $body['type'],
            $body['description'],
            $body['date'],
            $body['currency'],
            $body['reversalOf'],
            $body['extra'],
            $eventId,
            $occurredOn
        );
    }
}
