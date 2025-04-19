<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain\Enums;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
    
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
    
    public function isClosed(): bool
    {
        return $this === self::CLOSED;
    }
}
