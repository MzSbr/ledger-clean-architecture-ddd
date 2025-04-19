<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\CreateJournalEntry;

use App\Journal\Entry\Domain\Enums\EntryType;
use App\Journal\Entry\Domain\JournalEntry;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Journal\Entry\Domain\ValueObjects\Description;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Ledger\Currency\Domain\ValueObjects\Currency;
use App\Shared\Application\CommandHandler;
use DateTimeImmutable;

final class CreateJournalEntryHandler implements CommandHandler
{
    public function __construct(
        private readonly JournalEntryRepository $journalEntryRepository
    ) {
    }

    public function __invoke(CreateJournalEntryCommand $command): void
    {
        $id = new JournalEntryId($command->id);
        $type = EntryType::from($command->type);
        $description = new Description($command->description);
        $date = new DateTimeImmutable($command->date);
        $currency = new Currency($command->currency);
        $reversalOf = $command->reversalOf ? new JournalEntryId($command->reversalOf) : null;

        $journalEntry = JournalEntry::create(
            $id,
            $type,
            $description,
            $date,
            $currency,
            $reversalOf,
            $command->extra
        );

        $this->journalEntryRepository->save($journalEntry);
    }
}
