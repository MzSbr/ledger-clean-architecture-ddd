<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Ramsey\Uuid\Uuid;

abstract class UuidValueObject
{
    protected string $value;

    public function __construct(?string $value = null)
    {
        $this->value = $value ?: Uuid::uuid4()->toString();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UuidValueObject $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
