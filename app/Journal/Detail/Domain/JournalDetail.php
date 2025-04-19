<?php

declare(strict_types=1);

namespace App\Journal\Detail\Domain;

use App\Journal\Detail\Domain\ValueObjects\JournalDetailId;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Ledger\Currency\Domain\ValueObjects\Money;
use App\Shared\Domain\Entity;

final class JournalDetail extends Entity
{
    private JournalDetailId $id;
    private JournalEntryId $journalEntryId;
    private AccountId $accountId;
    private Money $money;
    private ?string $memo;
    private ?array $extra;
    
    private function __construct(
        JournalDetailId $id,
        JournalEntryId $journalEntryId,
        AccountId $accountId,
        Money $money,
        ?string $memo = null,
        ?array $extra = null
    ) {
        $this->id = $id;
        $this->journalEntryId = $journalEntryId;
        $this->accountId = $accountId;
        $this->money = $money;
        $this->memo = $memo;
        $this->extra = $extra;
    }
    
    public static function create(
        JournalDetailId $id,
        JournalEntryId $journalEntryId,
        AccountId $accountId,
        Money $money,
        ?string $memo = null,
        ?array $extra = null
    ): self {
        return new self($id, $journalEntryId, $accountId, $money, $memo, $extra);
    }
    
    public function reverse(): self
    {
        $reversedMoney = new Money(-$this->money->amount(), $this->money->currency());
        
        return new self(
            new JournalDetailId(),
            $this->journalEntryId,
            $this->accountId,
            $reversedMoney,
            $this->memo,
            $this->extra
        );
    }
    
    public function id(): JournalDetailId
    {
        return $this->id;
    }
    
    public function journalEntryId(): JournalEntryId
    {
        return $this->journalEntryId;
    }
    
    public function accountId(): AccountId
    {
        return $this->accountId;
    }
    
    public function money(): Money
    {
        return $this->money;
    }
    
    public function amount(): int
    {
        return $this->money->amount();
    }
    
    public function memo(): ?string
    {
        return $this->memo;
    }
    
    public function extra(): ?array
    {
        return $this->extra;
    }
    
    protected function identity(): string
    {
        return $this->id->value();
    }
}
