<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain;

use App\Ledger\Account\Domain\ValueObjects\AccountCode;
use App\Ledger\Account\Domain\ValueObjects\AccountId;

interface AccountRepository
{
    public function save(Account $account): void;
    
    public function findById(AccountId $id): ?Account;
    
    public function findByCode(AccountCode $code): ?Account;
    
    public function findChildren(AccountId $parentId): array;
    
    public function findAll(): array;
    
    public function exists(AccountId $id): bool;
    
    public function codeExists(AccountCode $code): bool;
}
