<?php

declare(strict_types=1);

namespace App\Ledger\Name\Domain;

use App\Ledger\Name\Domain\Name;
use App\Ledger\Name\Domain\ValueObjects\NameId;

interface NameRepository
{
    public function save(Name $name): void;
    
    public function findById(NameId $id): ?Name;
    
    public function findByOwnerUuid(string $ownerUuid): array;
    
    public function findByOwnerUuidAndLanguage(string $ownerUuid, string $language): ?Name;
    
    public function delete(Name $name): void;
}
