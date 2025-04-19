<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Commands\CreateAccount;

use App\Shared\Application\Command;

final class CreateAccountCommand implements Command
{
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $type,
        public readonly ?string $parentId,
        public readonly bool $isCategory = false,
        public readonly ?string $taxCode = null,
        public readonly ?array $extra = null
    ) {
    }
}
