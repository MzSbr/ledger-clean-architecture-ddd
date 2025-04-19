<?php

declare(strict_types=1);

namespace App\Journal\Entry\Domain;

use App\Journal\Entry\Domain\Enums\EntryStatus;
use App\Journal\Entry\Domain\Enums\EntryType;
use App\Journal\Entry\Domain\Events\JournalEntryCreated;
use App\Journal\Entry\Domain\Events\JournalEntryPosted;
use App\Journal\Entry\Domain\Events\JournalEntryReversed;
use App\Journal\Entry\Domain\Exceptions\JournalEntryAlreadyPostedException;
use App\Journal\Entry\Domain\Exceptions\JournalEntryAlreadyReversedException;
use App\Journal\Entry\Domain\Exceptions\JournalEntryNotBalancedException;
use App\Journal\Entry\Domain\ValueObjects\Description;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Ledger\Currency\Domain\ValueObjects\Currency;
use App\Journal\Detail\Domain\JournalDetail;
use App\Shared\Domain\AggregateRoot;
use DateTimeImmutable;

final class JournalEntry extends AggregateRoot
{
    private JournalEntryId $id;
    private EntryType $type;
    private EntryStatus $status;
    private Description $description;
    private DateTimeImmutable $date;
    private Currency $currency;
    private ?JournalEntryId $reversalOf;
    private array $details;
    private ?array $extra;
    
    private function __construct(
        JournalEntryId $id,
        EntryType $type,
        Description $description,
        DateTimeImmutable $date,
        Currency $currency,
        ?JournalEntryId $reversalOf = null,
        ?array $extra = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->status = EntryStatus::DRAFT;
        $this->description = $description;
        $this->date = $date;
        $this->currency = $currency;
        $this->reversalOf = $reversalOf;
        $this->details = [];
        $this->extra = $extra;
    }
    
    public static function create(
        JournalEntryId $id,
        EntryType $type,
        Description $description,
        DateTimeImmutable $date,
        Currency $currency,
        ?JournalEntryId $reversalOf = null,
        ?array $extra = null
    ): self {
        $entry = new self($id, $type, $description, $date, $currency, $reversalOf, $extra);
        
        $entry->record(new JournalEntryCreated(
            $id->value(),
            $type->value,
            $description->value(),
            $date->format('Y-m-d H:i:s'),
            $currency->value(),
            $reversalOf ? $reversalOf->value() : null,
            $extra
        ));
        
        return $entry;
    }

    public static function fromPrimitives(
        JournalEntryId $id,
        EntryType $type,
        EntryStatus $status,
        Description $description,
        DateTimeImmutable $date,
        Currency $currency,
        ?JournalEntryId $reversalOf,
        array $extra
    ): self {
        $instance = new self($id, $type, $description, $date, $currency, $reversalOf, $extra);
        $instance->id = $id;
        $instance->type = $type;
        $instance->status = $status;
        $instance->description = $description;
        $instance->date = $date;
        $instance->currency = $currency;
        $instance->reversalOf = $reversalOf;
        $instance->extra = $extra;

        return $instance;
        
    }
    public function addDetail(JournalDetail $detail): void
    {
        if ($this->status !== EntryStatus::DRAFT) {
            throw new JournalEntryAlreadyPostedException('Cannot add details to a posted or reversed journal entry');
        }
        
        $this->details[] = $detail;
    }
    
    public function post(): void
    {
        if ($this->status !== EntryStatus::DRAFT) {
            throw new JournalEntryAlreadyPostedException('Journal entry is already posted or reversed');
        }
        
        if (!$this->isBalanced()) {
            throw new JournalEntryNotBalancedException('Journal entry must be balanced before posting');
        }
        
        $this->status = EntryStatus::POSTED;
        
        $this->record(new JournalEntryPosted(
            $this->id->value()
        ));
    }
    
    public function reverse(
        JournalEntryId $reversalId,
        Description $description,
        DateTimeImmutable $date
    ): JournalEntry {
        if ($this->status !== EntryStatus::POSTED) {
            throw new JournalEntryAlreadyReversedException('Only posted journal entries can be reversed');
        }
        
        $reversal = self::create(
            $reversalId,
            EntryType::REVERSAL,
            $description,
            $date,
            $this->currency,
            $this->id,
            $this->extra
        );
        
        // Create reversed details
        foreach ($this->details as $detail) {
            $reversal->addDetail($detail->reverse());
        }
        
        $this->status = EntryStatus::REVERSED;
        
        $this->record(new JournalEntryReversed(
            $this->id->value(),
            $reversalId->value()
        ));
        
        return $reversal;
    }
    
    public function isBalanced(): bool
    {
        $total = 0;
        
        foreach ($this->details as $detail) {
            $total += $detail->amount();
        }
        
        return $total === 0;
    }
    
    public function id(): JournalEntryId
    {
        return $this->id;
    }
    
    public function type(): EntryType
    {
        return $this->type;
    }
    
    public function status(): EntryStatus
    {
        return $this->status;
    }
    
    public function description(): Description
    {
        return $this->description;
    }
    
    public function date(): DateTimeImmutable
    {
        return $this->date;
    }
    
    public function currency(): Currency
    {
        return $this->currency;
    }
    
    public function reversalOf(): ?JournalEntryId
    {
        return $this->reversalOf;
    }
    
    public function details(): array
    {
        return $this->details;
    }
    
    public function extra(): ?array
    {
        return $this->extra;
    }
    
    public function isDraft(): bool
    {
        return $this->status->isDraft();
    }
    
    public function isPosted(): bool
    {
        return $this->status->isPosted();
    }
    
    public function isReversed(): bool
    {
        return $this->status->isReversed();
    }
}
