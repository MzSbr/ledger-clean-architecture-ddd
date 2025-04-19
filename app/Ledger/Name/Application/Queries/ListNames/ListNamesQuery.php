<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Queries\ListNames;

final class ListNamesQuery
{
    public function __construct(
        private string $ownerUuid,
        private ?string $language = null
    ) {
    }

    public function getOwnerUuid(): string
    {
        return $this->ownerUuid;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }
}
