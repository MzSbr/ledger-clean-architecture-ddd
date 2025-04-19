<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Queries\ListNames;

use App\Ledger\Name\Domain\NameRepository;

final class ListNamesHandler
{
    public function __construct(
        private NameRepository $nameRepository
    ) {
    }

    public function __invoke(ListNamesQuery $query): array
    {
        if ($query->getLanguage() !== null) {
            $name = $this->nameRepository->findByOwnerUuidAndLanguage(
                $query->getOwnerUuid(),
                $query->getLanguage()
            );
            
            return $name ? [$name] : [];
        }
        
        return $this->nameRepository->findByOwnerUuid($query->getOwnerUuid());
    }
}
