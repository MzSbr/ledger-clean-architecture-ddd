<?php

namespace tests\Unit\Journal\Entry\Domain;

use App\Journal\Entry\Domain\Enums\EntryStatus;
use App\Journal\Entry\Domain\Enums\EntryType;
use App\Journal\Entry\Domain\Exceptions\JournalEntryAlreadyPostedException;
use App\Journal\Entry\Domain\Exceptions\JournalEntryNotBalancedException;
use App\Journal\Entry\Domain\JournalEntry;
use App\Journal\Entry\Domain\ValueObjects\Description;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Journal\Detail\Domain\JournalDetail;
use App\Journal\Detail\Domain\ValueObjects\JournalDetailId;
use App\Ledger\Account\Domain\ValueObjects\AccountId;
use App\Ledger\Currency\Domain\ValueObjects\Currency;
use App\Ledger\Currency\Domain\ValueObjects\Money;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class JournalEntryTest extends TestCase
{
    private JournalEntryId $id;
    private EntryType $type;
    private Description $description;
    private DateTimeImmutable $date;
    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->id = new JournalEntryId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
        $this->type = EntryType::REGULAR;
        $this->description = new Description('Test Journal Entry');
        $this->date = new DateTimeImmutable('2025-04-18');
        $this->currency = new Currency('USD');
    }

    public function testCreateJournalEntry(): void
    {
        $journalEntry = JournalEntry::create(
            $this->id,
            $this->type,
            $this->description,
            $this->date,
            $this->currency,
            null,
            null
        );

        $this->assertEquals($this->id, $journalEntry->id());
        $this->assertEquals($this->type, $journalEntry->type());
        $this->assertEquals(EntryStatus::DRAFT, $journalEntry->status());
        $this->assertEquals($this->description, $journalEntry->description());
        $this->assertEquals($this->date, $journalEntry->date());
        $this->assertEquals($this->currency, $journalEntry->currency());
        $this->assertNull($journalEntry->reversalOf());
        $this->assertNull($journalEntry->extra());
    }

    public function testAddJournalDetail(): void
    {
        $journalEntry = JournalEntry::create(
            $this->id,
            $this->type,
            $this->description,
            $this->date,
            $this->currency,
            null,
            null
        );

        $detailId = new JournalDetailId('a47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId = new AccountId('b47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money = new Money(1000, $this->currency);

        $detail = JournalDetail::create(
            $detailId,
            $this->id,
            $accountId,
            $money,
            null,
            null
        );

        $journalEntry->addDetail($detail);

        $this->assertCount(1, $journalEntry->details());
    }

    public function testPostJournalEntryWithoutBalanceThrowsException(): void
    {
        $journalEntry = JournalEntry::create(
            $this->id,
            $this->type,
            $this->description,
            $this->date,
            $this->currency,
            null,
            null
        );

        $detailId = new JournalDetailId('a47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId = new AccountId('b47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money = new Money(1000, $this->currency);

        $detail = JournalDetail::create(
            $detailId,
            $this->id,
            $accountId,
            $money,
            null,
            null
        );

        $journalEntry->addDetail($detail);

        $this->expectException(JournalEntryNotBalancedException::class);
        $journalEntry->post();
    }

    public function testPostBalancedJournalEntry(): void
    {
        $journalEntry = JournalEntry::create(
            $this->id,
            $this->type,
            $this->description,
            $this->date,
            $this->currency,
            null,
            null
        );

        $detailId1 = new JournalDetailId('a47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId1 = new AccountId('b47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money1 = new Money(1000, $this->currency);

        $detail1 = JournalDetail::create(
            $detailId1,
            $this->id,
            $accountId1,
            $money1,
            null,
            null
        );

        $detailId2 = new JournalDetailId('c47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId2 = new AccountId('d47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money2 = new Money(-1000, $this->currency);

        $detail2 = JournalDetail::create(
            $detailId2,
            $this->id,
            $accountId2,
            $money2,
            null,
            null
        );

        $journalEntry->addDetail($detail1);
        $journalEntry->addDetail($detail2);

        $journalEntry->post();

        $this->assertEquals(EntryStatus::POSTED, $journalEntry->status());
    }

    public function testAddDetailToPostedJournalEntryThrowsException(): void
    {
        $journalEntry = JournalEntry::create(
            $this->id,
            $this->type,
            $this->description,
            $this->date,
            $this->currency,
            null,
            null
        );

        $detailId1 = new JournalDetailId('a47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId1 = new AccountId('b47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money1 = new Money(1000, $this->currency);

        $detail1 = JournalDetail::create(
            $detailId1,
            $this->id,
            $accountId1,
            $money1,
            null,
            null
        );

        $detailId2 = new JournalDetailId('c47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId2 = new AccountId('d47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money2 = new Money(-1000, $this->currency);

        $detail2 = JournalDetail::create(
            $detailId2,
            $this->id,
            $accountId2,
            $money2,
            null,
            null
        );

        $journalEntry->addDetail($detail1);
        $journalEntry->addDetail($detail2);

        $journalEntry->post();

        $detailId3 = new JournalDetailId('e47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId3 = new AccountId('f47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money3 = new Money(0, $this->currency);

        $detail3 = JournalDetail::create(
            $detailId3,
            $this->id,
            $accountId3,
            $money3,
            null,
            null
        );

        $this->expectException(JournalEntryAlreadyPostedException::class);
        $journalEntry->addDetail($detail3);
    }

    public function testReverseJournalEntry(): void
    {
        $journalEntry = JournalEntry::create(
            $this->id,
            $this->type,
            $this->description,
            $this->date,
            $this->currency,
            null,
            null
        );

        $detailId1 = new JournalDetailId('a47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId1 = new AccountId('b47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money1 = new Money(1000, $this->currency);

        $detail1 = JournalDetail::create(
            $detailId1,
            $this->id,
            $accountId1,
            $money1,
            null,
            null
        );

        $detailId2 = new JournalDetailId('c47ac10b-58cc-4372-a567-0e02b2c3d479');
        $accountId2 = new AccountId('d47ac10b-58cc-4372-a567-0e02b2c3d479');
        $money2 = new Money(-1000, $this->currency);

        $detail2 = JournalDetail::create(
            $detailId2,
            $this->id,
            $accountId2,
            $money2,
            null,
            null
        );

        $journalEntry->addDetail($detail1);
        $journalEntry->addDetail($detail2);

        $journalEntry->post();

        $reversalId = new JournalEntryId('g47ac10b-58cc-4372-a567-0e02b2c3d479');
        $reversalDescription = new Description('Reversal of Test Journal Entry');
        $reversalDate = new DateTimeImmutable('2025-04-19');

        $reversalEntry = $journalEntry->reverse($reversalId, $reversalDescription, $reversalDate);

        $this->assertEquals(EntryStatus::REVERSED, $journalEntry->status());
        $this->assertEquals(EntryType::REVERSAL, $reversalEntry->type());
        $this->assertEquals($reversalDescription, $reversalEntry->description());
        $this->assertEquals($reversalDate, $reversalEntry->date());
        $this->assertEquals($this->id, $reversalEntry->reversalOf());
        $this->assertCount(2, $reversalEntry->details());
    }
}
