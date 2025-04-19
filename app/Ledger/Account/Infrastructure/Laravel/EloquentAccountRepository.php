<?php

declare(strict_types=1);

namespace App\Ledger\Account\Infrastructure\Laravel;

use App\Ledger\Account\Domain\Account;
use App\Ledger\Account\Domain\AccountRepository;
use App\Ledger\Account\Domain\ValueObjects\AccountCode;
use App\Ledger\Account\Domain\ValueObjects\AccountId;

final class EloquentAccountRepository implements AccountRepository
{
    public function save(Account $account): void
    {
        $model = AccountEloquentModel::fromDomain($account);
        $model->save();
    }
    
    public function findById(AccountId $id): ?Account
    {
        $model = AccountEloquentModel::find($id->value());
        
        if ($model === null) {
            return null;
        }
        
        return $model->toDomain();
    }
    
    public function findByCode(AccountCode $code): ?Account
    {
        $model = AccountEloquentModel::where('code', $code->value())->first();
        
        if ($model === null) {
            return null;
        }
        
        return $model->toDomain();
    }
    
    public function findChildren(AccountId $parentId): array
    {
        $models = AccountEloquentModel::where('parent_id', $parentId->value())->get();
        
        return $models->map(function (AccountEloquentModel $model) {
            return $model->toDomain();
        })->toArray();
    }
    
    public function findAll(): array
    {
        $models = AccountEloquentModel::all();
        
        return $models->map(function (AccountEloquentModel $model) {
            return $model->toDomain();
        })->toArray();
    }
    
    public function exists(AccountId $id): bool
    {
        return AccountEloquentModel::where('id', $id->value())->exists();
    }
    
    public function codeExists(AccountCode $code): bool
    {
        return AccountEloquentModel::where('code', $code->value())->exists();
    }
}
