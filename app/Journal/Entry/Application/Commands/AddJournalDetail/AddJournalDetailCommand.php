<?php

declare(strict_types=1);

namespace App\Journal\Entry\Application\Commands\AddJournalDetail;

use App\Shared\Application\Command;

final class AddJournalDetailCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $journalEntryId,
        public readonly string $accountId,
        public readonly int $amount,
        public readonly string $currency,
        public readonly ?string $memo = null,
        public readonly ?array $extra = null
    ) {
    }
}
