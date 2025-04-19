<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Commands\UpdateAccount;

use App\Shared\Application\Command;

final class UpdateAccountCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly ?string $taxCode = null,
        public readonly ?array $extra = null
    ) {
    }
}
