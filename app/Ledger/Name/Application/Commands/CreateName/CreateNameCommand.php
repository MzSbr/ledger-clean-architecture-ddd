<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Commands\CreateName;

final class CreateNameCommand
{
    public function __construct(
        private string $name,
        private string $language,
        private string $ownerUuid
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getOwnerUuid(): string
    {
        return $this->ownerUuid;
    }
}
