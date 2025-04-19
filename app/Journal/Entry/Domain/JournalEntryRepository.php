<?php

declare(strict_types=1);

namespace App\Journal\Entry\Domain;

use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;

interface JournalEntryRepository
{
    public function save(JournalEntry $journalEntry): void;
    
    public function findById(JournalEntryId $id): ?JournalEntry;
    
    public function findByIds(array $ids): array;
    
    public function findAll(): array;
    
    public function exists(JournalEntryId $id): bool;
}
