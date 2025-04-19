<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain;

use App\Ledger\Account\Domain\Enums\AccountStatus;
use App\Ledger\Account\Domain\Enums\AccountType;
use App\Ledger\Account\Domain\Events\AccountCreated;
use App\Ledger\Account\Domain\Events\AccountClosed;
use App\Ledger\Account\Domain\Events\AccountUpdated;
use App\Ledger\Account\Domain\Exceptions\AccountClosedException;
use App\Ledger\Account\Domain\ValueObjects\AccountCode;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Shared\Domain\AggregateRoot;

final class Account extends AggregateRoot
{
    private AccountId $id;
    private AccountCode $code;
    private AccountType $type;
    private AccountStatus $status;
    private ?AccountId $parentId;
    private bool $isCategory;
    private ?string $taxCode;
    private ?array $extra;

    private function __construct(
        AccountId $id,
        AccountCode $code,
        AccountType $type,
        ?AccountId $parentId,
        bool $isCategory = false,
        ?string $taxCode = null,
        ?array $extra = null
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
        $this->parentId = $parentId;
        $this->status = AccountStatus::ACTIVE;
        $this->isCategory = $isCategory;
        $this->taxCode = $taxCode;
        $this->extra = $extra;
    }

    public static function create(
        AccountId $id,
        AccountCode $code,
        AccountType $type,
        ?AccountId $parentId,
        bool $isCategory = false,
        ?string $taxCode = null,
        ?array $extra = null
    ): self {
        $account = new self($id, $code, $type, $parentId, $isCategory, $taxCode, $extra);

        $account->record(new AccountCreated(
            $id->value(),
            $code->value(),
            $type->value,
            $parentId ? $parentId->value() : null,
            $isCategory,
            $taxCode,
            $extra
        ));

        return $account;
    }

    public static function fromPrimitives(
        AccountId $id,
        AccountCode $code,
        AccountType $type,
        AccountStatus $status,
        ?AccountId $parentId,
        bool $isCategory,
        ?string $taxCode,
        array $extra
    ): self {
        $account = new self(
            $id,
            $code,
            $type,
            $parentId,
            $isCategory,
            $taxCode,
            $extra
        );
        $account->status = $status;

        return $account;
    }

    public function update(
        AccountCode $code,
        ?string $taxCode = null,
        ?array $extra = null
    ): void {
        if ($this->status->isClosed()) {
            throw new AccountClosedException('Cannot update a closed account');
        }

        $this->code = $code;
        $this->taxCode = $taxCode;
        $this->extra = $extra;

        $this->record(new AccountUpdated(
            $this->id->value(),
            $code->value(),
            $taxCode,
            $extra
        ));
    }

    public function close(): void
    {
        if ($this->status->isClosed()) {
            return;
        }

        $this->status = AccountStatus::CLOSED;

        $this->record(new AccountClosed(
            $this->id->value()
        ));
    }

    public function id(): AccountId
    {
        return $this->id;
    }

    public function code(): AccountCode
    {
        return $this->code;
    }

    public function type(): AccountType
    {
        return $this->type;
    }

    public function status(): AccountStatus
    {
        return $this->status;
    }

    public function parentId(): ?AccountId
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

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isClosed(): bool
    {
        return $this->status->isClosed();
    }

    public function isDebit(): bool
    {
        return $this->type->isDebit();
    }

    public function isCredit(): bool
    {
        return $this->type->isCredit();
    }
}
