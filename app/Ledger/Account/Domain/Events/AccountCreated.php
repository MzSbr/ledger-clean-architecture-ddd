<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain\Events;

use App\Shared\Domain\DomainEvent;

final class AccountCreated extends DomainEvent
{
    private string $code;
    private string $type;
    private ?string $parentId;
    private bool $isCategory;
    private ?string $taxCode;
    private ?array $extra;

    public function __construct(
        string $id,
        string $code,
        string $type,
        ?string $parentId,
        bool $isCategory,
        ?string $taxCode,
        ?array $extra,
        string $eventId = null,
        string $occurredOn = null
    ) {
        parent::__construct($id, $eventId, $occurredOn);
        $this->code = $code;
        $this->type = $type;
        $this->parentId = $parentId;
        $this->isCategory = $isCategory;
        $this->taxCode = $taxCode;
        $this->extra = $extra;
    }

    public function eventName(): string
    {
        return 'account.created';
    }

    public function code(): string
    {
        return $this->code;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function isCategory(): bool
    {
        return $this->isCategory;
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
            'type' => $this->type,
            'parentId' => $this->parentId,
            'isCategory' => $this->isCategory,
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
            $body['type'],
            $body['parentId'],
            $body['isCategory'],
            $body['taxCode'],
            $body['extra'],
            $eventId,
            $occurredOn
        );
    }
}
