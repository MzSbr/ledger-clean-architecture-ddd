<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Commands\UpdateName;

final class UpdateNameCommand
{
    public function __construct(
        private string $id,
        private string $name,
        private string $language
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
