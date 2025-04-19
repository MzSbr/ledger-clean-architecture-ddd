<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Commands\CreateName;

use App\Ledger\Name\Domain\Name;
use App\Ledger\Name\Domain\NameRepository;
use App\Ledger\Name\Domain\ValueObjects\NameId;

final class CreateNameHandler
{
    public function __construct(
        private NameRepository $nameRepository
    ) {
    }

    public function __invoke(CreateNameCommand $command): void
    {
        $name = Name::create(
            new NameId(),
            $command->getName(),
            $command->getLanguage(),
            $command->getOwnerUuid()
        );

        $this->nameRepository->save($name);
    }
}
