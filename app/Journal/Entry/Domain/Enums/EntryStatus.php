<?php

declare(strict_types=1);

namespace App\Journal\Entry\Domain\Enums;

enum EntryStatus: string
{
    case DRAFT = 'draft';
    case POSTED = 'posted';
    case REVERSED = 'reversed';
    
    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }
    
    public function isPosted(): bool
    {
        return $this === self::POSTED;
    }
    
    public function isReversed(): bool
    {
        return $this === self::REVERSED;
    }
}
