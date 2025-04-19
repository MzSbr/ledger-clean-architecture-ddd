<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Queries\GetAccount;

use App\Shared\Application\Query;

final class GetAccountQuery implements Query
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
