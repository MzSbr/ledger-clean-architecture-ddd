<?php

declare(strict_types=1);

namespace App\Ledger\Currency\Domain\ValueObjects;

use App\Ledger\Currency\Domain\Exceptions\InvalidCurrencyException;

final class Currency
{
    private const MAX_LENGTH = 3;
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = strtoupper($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Currency $other): bool
    {
        return $this->value() === $other->value();
    }

    private function validate(string $value): void
    {
        if (empty($value)) {
            throw new InvalidCurrencyException('Currency code cannot be empty');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidCurrencyException(
                sprintf('Currency code cannot be longer than %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
