<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Commands\DeleteName;

use App\Ledger\Name\Domain\NameRepository;
use App\Ledger\Name\Domain\ValueObjects\NameId;
use App\Ledger\Name\Domain\Exceptions\NameNotFoundException;

final class DeleteNameHandler
{
    public function __construct(
        private NameRepository $nameRepository
    ) {
    }

    public function __invoke(DeleteNameCommand $command): void
    {
        $name = $this->nameRepository->findById(new NameId($command->getId()));
        
        if ($name === null) {
            throw new NameNotFoundException("Name with ID {$command->getId()} not found");
        }
        
        $this->nameRepository->delete($name);
    }
}
