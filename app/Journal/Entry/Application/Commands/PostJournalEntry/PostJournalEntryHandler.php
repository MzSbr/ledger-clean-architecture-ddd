<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\PostJournalEntry;

use App\Journal\Entry\Domain\Exceptions\JournalEntryNotFoundException;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;
use App\Shared\Application\CommandHandler;

final class PostJournalEntryHandler implements CommandHandler
{
    public function __construct(
        private readonly JournalEntryRepository $journalEntryRepository
    ) {
    }

    public function __invoke(PostJournalEntryCommand $command): void
    {
        $id = new JournalEntryId($command->id);

        $journalEntry = $this->journalEntryRepository->findById($id);

        if ($journalEntry === null) {
            throw new JournalEntryNotFoundException("Journal entry with ID {$command->id} not found");
        }

        $journalEntry->post();

        $this->journalEntryRepository->save($journalEntry);
    }
}
