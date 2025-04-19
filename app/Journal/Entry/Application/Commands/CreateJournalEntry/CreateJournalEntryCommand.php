<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\CreateJournalEntry;

use App\Shared\Application\Command;

final class CreateJournalEntryCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $description,
        public readonly string $date,
        public readonly string $currency,
        public readonly ?string $reversalOf = null,
        public readonly ?array $extra = null
    ) {
    }
}
