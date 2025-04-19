<?php

declare(strict_types=1);

namespace App\Journal\Entry\Domain\Enums;

enum EntryType: string
{
    case REGULAR = 'regular';
    case ADJUSTMENT = 'adjustment';
    case CLOSING = 'closing';
    case OPENING = 'opening';
    case REVERSAL = 'reversal';
    
    public function isRegular(): bool
    {
        return $this === self::REGULAR;
    }
    
    public function isAdjustment(): bool
    {
        return $this === self::ADJUSTMENT;
    }
    
    public function isClosing(): bool
    {
        return $this === self::CLOSING;
    }
    
    public function isOpening(): bool
    {
        return $this === self::OPENING;
    }
    
    public function isReversal(): bool
    {
        return $this === self::REVERSAL;
    }
}
