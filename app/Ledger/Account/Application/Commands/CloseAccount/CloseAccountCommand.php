<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Commands\CloseAccount;

use App\Shared\Application\Command;

final class CloseAccountCommand implements Command
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
