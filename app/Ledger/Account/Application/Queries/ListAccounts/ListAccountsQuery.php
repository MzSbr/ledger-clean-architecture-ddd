<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Queries\ListAccounts;

use App\Shared\Application\Query;

final class ListAccountsQuery implements Query
{
    public function __construct(
        public readonly ?string $parentId = null
    ) {
    }
}
