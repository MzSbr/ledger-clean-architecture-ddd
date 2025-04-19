<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\ReverseJournalEntry;

use App\Journal\Entry\Domain\Exceptions\JournalEntryNotFoundException;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Journal\Entry\Domain\ValueObjects\Description;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Shared\Application\CommandHandler;
use DateTimeImmutable;

final class ReverseJournalEntryHandler implements CommandHandler
{
    public function __construct(
        private readonly JournalEntryRepository $journalEntryRepository
    ) {
    }

    public function __invoke(ReverseJournalEntryCommand $command): void
    {
        $id = new JournalEntryId($command->id);
        $reversalId = new JournalEntryId($command->reversalId);
        $description = new Description($command->description);
        $date = new DateTimeImmutable($command->date);

        $journalEntry = $this->journalEntryRepository->findById($id);

        if ($journalEntry === null) {
            throw new JournalEntryNotFoundException("Journal entry with ID {$command->id} not found");
        }

        $reversalEntry = $journalEntry->reverse($reversalId, $description, $date);

        $this->journalEntryRepository->save($journalEntry);
        $this->journalEntryRepository->save($reversalEntry);
    }
}
