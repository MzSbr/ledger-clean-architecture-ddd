<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\ReverseJournalEntry;

use App\Shared\Application\Command;

final class ReverseJournalEntryCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $reversalId,
        public readonly string $description,
        public readonly string $date
    ) {
    }
}
