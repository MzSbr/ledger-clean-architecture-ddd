<?php

declare(strict_types=1);

namespace App\Journal\Entry\Domain\ValueObjects;

use App\Journal\Entry\Domain\Exceptions\InvalidDescriptionException;

final class Description
{
    private const MAX_LENGTH = 255;

    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Description $other): bool
    {
        return $this->value() === $other->value();
    }

    private function validate(string $value): void
    {
        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidDescriptionException(
                sprintf('Description cannot be longer than %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
