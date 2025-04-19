<?php

declare(strict_types=1);

namespace App\Ledger\Account\Application\Listeners;

use App\Ledger\Account\Domain\Events\AccountCreated;
use Illuminate\Support\Facades\Log;

final class LogAccountCreated
{
    public function __invoke(AccountCreated $event): void
    {
        Log::info('Account created with ID: ' . $event->aggregateId());
        Log::info('Account details: ', $event->toPrimitives());
    }
}