<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain\Enums;

enum AccountType: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
    
    public function isDebit(): bool
    {
        return $this === self::DEBIT;
    }
    
    public function isCredit(): bool
    {
        return $this === self::CREDIT;
    }
}
