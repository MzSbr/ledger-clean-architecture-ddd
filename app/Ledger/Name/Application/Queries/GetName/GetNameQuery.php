<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Queries\GetName;

final class GetNameQuery
{
    public function __construct(
        private string $id
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
