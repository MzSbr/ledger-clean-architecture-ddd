<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Queries\GetJournalEntry;

use App\Shared\Application\Query;

final class GetJournalEntryQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
