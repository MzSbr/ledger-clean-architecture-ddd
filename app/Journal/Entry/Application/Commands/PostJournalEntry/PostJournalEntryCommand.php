<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\PostJournalEntry;

use App\Shared\Application\Command;

final class PostJournalEntryCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
