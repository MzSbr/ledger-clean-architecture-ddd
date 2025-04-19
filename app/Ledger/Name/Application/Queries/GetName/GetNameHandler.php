<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Queries\GetName;

use App\Ledger\Name\Domain\Name;
use App\Ledger\Name\Domain\NameRepository;
use App\Ledger\Name\Domain\ValueObjects\NameId;
use App\Ledger\Name\Domain\Exceptions\NameNotFoundException;

final class GetNameHandler
{
    public function __construct(
        private NameRepository $nameRepository
    ) {
    }

    public function __invoke(GetNameQuery $query): Name
    {
        $name = $this->nameRepository->findById(new NameId($query->getId()));
        
        if ($name === null) {
            throw new NameNotFoundException("Name with ID {$query->getId()} not found");
        }
        
        return $name;
    }
}
