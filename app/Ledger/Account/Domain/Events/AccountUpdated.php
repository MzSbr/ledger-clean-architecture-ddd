<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain\Events;

use App\Shared\Domain\DomainEvent;

final class AccountUpdated extends DomainEvent
{
    private string $code;
    private ?string $taxCode;
    private ?array $extra;

    public function __construct(
        string $id,
        string $code,
        ?string $taxCode,
        ?array $extra,
        string $eventId = null,
        string $occurredOn = null
    ) {
        parent::__construct($id, $eventId, $occurredOn);
        $this->code = $code;
        $this->taxCode = $taxCode;
        $this->extra = $extra;
    }

    public function eventName(): string
    {
        return 'account.updated';
    }

    public function code(): string
    {
        return $this->code;
    }

    public function taxCode(): ?string
    {
        return $this->taxCode;
    }

    public function extra(): ?array
    {
        return $this->extra;
    }

    public function toPrimitives(): array
    {
        return [
            'code' => $this->code,
            'taxCode' => $this->taxCode,
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
            $body['code'],
            $body['taxCode'],
            $body['extra'],
            $eventId,
            $occurredOn
        );
    }
}
