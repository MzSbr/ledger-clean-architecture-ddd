<?php

declare(strict_types=1);

namespace App\Journal\Detail\Domain;

use App\Journal\Detail\Domain\ValueObjects\JournalDetailId;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;

interface JournalDetailRepository
{
    public function save(JournalDetail $journalDetail): void;
    
    public function findById(JournalDetailId $id): ?JournalDetail;
    
    public function findByJournalEntryId(JournalEntryId $journalEntryId): array;
    
    public function exists(JournalDetailId $id): bool;
}
