<?php

declare(strict_types=1);

namespace App\Ledger\Account\Domain\ValueObjects;

use App\Ledger\Account\Domain\Exceptions\InvalidAccountCodeException;

final class AccountCode
{
    private const MAX_LENGTH = 32;

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

    public function equals(AccountCode $other): bool
    {
        return $this->value() === $other->value();
    }

    private function validate(string $value): void
    {
        if (empty($value) && $value !== '') {
            throw new InvalidAccountCodeException('Account code cannot be null');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidAccountCodeException(
                sprintf('Account code cannot be longer than %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
