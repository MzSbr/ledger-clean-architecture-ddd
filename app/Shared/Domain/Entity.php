<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class Entity
{
    public function equals(Entity $other): bool
    {
        return $this->identity() === $other->identity();
    }
    
    abstract protected function identity(): string;
}
