<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Commands\UpdateName;

use App\Ledger\Name\Domain\NameRepository;
use App\Ledger\Name\Domain\ValueObjects\NameId;
use App\Ledger\Name\Domain\Exceptions\NameNotFoundException;

final class UpdateNameHandler
{
    public function __construct(
        private NameRepository $nameRepository
    ) {
    }

    public function __invoke(UpdateNameCommand $command): void
    {
        $name = $this->nameRepository->findById(new NameId($command->getId()));
        
        if ($name === null) {
            throw new NameNotFoundException("Name with ID {$command->getId()} not found");
        }
        
        $name->updateName($command->getName());
        
        if ($command->getLanguage() !== $name->language()) {
            $name->updateLanguage($command->getLanguage());
        }
        
        $this->nameRepository->save($name);
    }
}
