<?php

declare(strict_types=1);

namespace App\Journal\Entry\Infrastructure\Laravel;

use App\Journal\Entry\Domain\JournalEntry;
use App\Journal\Entry\Domain\JournalEntryRepository;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;

final class EloquentJournalEntryRepository implements JournalEntryRepository
{
    public function save(JournalEntry $journalEntry): void
    {
        $model = JournalEntryEloquentModel::fromDomain($journalEntry);
        $model->save();
    }
    
    public function findById(JournalEntryId $id): ?JournalEntry
    {
        $model = JournalEntryEloquentModel::find($id->value());
        
        if ($model === null) {
            return null;
        }
        
        return $model->toDomain();
    }
    
    public function findByIds(array $ids): array
    {
        $idValues = array_map(function (JournalEntryId $id) {
            return $id->value();
        }, $ids);
        
        $models = JournalEntryEloquentModel::whereIn('id', $idValues)->get();
        
        return $models->map(function (JournalEntryEloquentModel $model) {
            return $model->toDomain();
        })->toArray();
    }
    
    public function findAll(): array
    {
        $models = JournalEntryEloquentModel::all();
        
        return $models->map(function (JournalEntryEloquentModel $model) {
            return $model->toDomain();
        })->toArray();
    }
    
    public function exists(JournalEntryId $id): bool
    {
        return JournalEntryEloquentModel::where('id', $id->value())->exists();
    }
}
