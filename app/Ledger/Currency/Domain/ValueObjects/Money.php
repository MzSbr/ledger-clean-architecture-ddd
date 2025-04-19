<?php

declare(strict_types=1);

namespace App\Ledger\Currency\Domain\ValueObjects;

use App\Ledger\Currency\Domain\Exceptions\InvalidMoneyException;
use App\Ledger\Currency\Domain\ValueObjects\Currency;

final class Money
{
    private int $amount;
    private Currency $currency;

    public function __construct(int $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function add(Money $money): self
    {
        $this->ensureSameCurrency($money);
        return new self($this->amount + $money->amount(), $this->currency);
    }

    public function subtract(Money $money): self
    {
        $this->ensureSameCurrency($money);
        return new self($this->amount - $money->amount(), $this->currency);
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function equals(Money $money): bool
    {
        return $this->amount === $money->amount() && $this->currency->equals($money->currency());
    }

    private function ensureSameCurrency(Money $money): void
    {
        if (!$this->currency->equals($money->currency())) {
            throw new InvalidMoneyException('Cannot operate on money with different currencies');
        }
    }

    public function __toString(): string
    {
        return sprintf('%d %s', $this->amount, $this->currency);
    }
}
