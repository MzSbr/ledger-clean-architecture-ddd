<?php

declare(strict_types=1);

namespace App\Ledger\Name\Application\Commands\DeleteName;

final class DeleteNameCommand
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
