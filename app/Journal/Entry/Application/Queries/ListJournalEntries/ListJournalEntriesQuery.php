<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Queries\ListJournalEntries;

use App\Shared\Application\Query;

final class ListJournalEntriesQuery implements Query
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $status = null,
        public readonly ?string $fromDate = null,
        public readonly ?string $toDate = null
    ) {
    }
}
