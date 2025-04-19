<?php

declare(strict_types=1);

namespace App\Journal\Detail\Infrastructure\Laravel;

use App\Journal\Detail\Domain\JournalDetail;
use App\Journal\Detail\Domain\JournalDetailRepository;
use App\Journal\Detail\Domain\ValueObjects\JournalDetailId;
use App\Journal\Entry\Domain\ValueObjects\JournalEntryId;

final class EloquentJournalDetailRepository implements JournalDetailRepository
{
    public function save(JournalDetail $journalDetail): void
    {
        $model = JournalDetailEloquentModel::fromDomain($journalDetail);
        $model->save();
    }
    
    public function findById(JournalDetailId $id): ?JournalDetail
    {
        $model = JournalDetailEloquentModel::find($id->value());
        
        if ($model === null) {
            return null;
        }
        
        return $model->toDomain();
    }
    
    public function findByJournalEntryId(JournalEntryId $journalEntryId): array
    {
        $models = JournalDetailEloquentModel::where('journal_entry_id', $journalEntryId->value())->get();
        
        return $models->map(function (JournalDetailEloquentModel $model) {
            return $model->toDomain();
        })->toArray();
    }
    
    public function exists(JournalDetailId $id): bool
    {
        return JournalDetailEloquentModel::where('id', $id->value())->exists();
    }
}
