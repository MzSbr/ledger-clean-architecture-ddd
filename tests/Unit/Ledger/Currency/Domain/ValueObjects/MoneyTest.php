<?php

namespace tests\Unit\Ledger\Currency\Domain\ValueObjects;

use App\Ledger\Currency\Domain\Exceptions\InvalidCurrencyException;
use App\Ledger\Currency\Domain\Exceptions\InvalidMoneyException;
use App\Ledger\Currency\Domain\ValueObjects\Currency;
use App\Ledger\Currency\Domain\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->currency = new Currency('USD');
    }

    public function testCreateMoney(): void
    {
        $money = new Money(1000, $this->currency);

        $this->assertEquals(1000, $money->amount());
        $this->assertEquals($this->currency, $money->currency());
    }

    public function testAddMoney(): void
    {
        $money1 = new Money(1000, $this->currency);
        $money2 = new Money(500, $this->currency);

        $result = $money1->add($money2);

        $this->assertEquals(1500, $result->amount());
        $this->assertEquals($this->currency, $result->currency());
    }

    public function testSubtractMoney(): void
    {
        $money1 = new Money(1000, $this->currency);
        $money2 = new Money(500, $this->currency);

        $result = $money1->subtract($money2);

        $this->assertEquals(500, $result->amount());
        $this->assertEquals($this->currency, $result->currency());
    }

    public function testMultiplyMoney(): void
    {
        $money = new Money(1000, $this->currency);

        $result = $money->multiply(2);

        $this->assertEquals(2000, $result->amount());
        $this->assertEquals($this->currency, $result->currency());
    }

    public function testIsPositive(): void
    {
        $money = new Money(1000, $this->currency);

        $this->assertTrue($money->isPositive());
        $this->assertFalse($money->isNegative());
        $this->assertFalse($money->isZero());
    }

    public function testIsNegative(): void
    {
        $money = new Money(-1000, $this->currency);

        $this->assertFalse($money->isPositive());
        $this->assertTrue($money->isNegative());
        $this->assertFalse($money->isZero());
    }

    public function testIsZero(): void
    {
        $money = new Money(0, $this->currency);

        $this->assertFalse($money->isPositive());
        $this->assertFalse($money->isNegative());
        $this->assertTrue($money->isZero());
    }

    public function testAddMoneyWithDifferentCurrencyThrowsException(): void
    {
        $money1 = new Money(1000, $this->currency);
        $money2 = new Money(500, new Currency('EUR'));

        $this->expectException(InvalidMoneyException::class);
        $money1->add($money2);
    }

    public function testSubtractMoneyWithDifferentCurrencyThrowsException(): void
    {
        $money1 = new Money(1000, $this->currency);
        $money2 = new Money(500, new Currency('EUR'));

        $this->expectException(InvalidMoneyException::class);
        $money1->subtract($money2);
    }

    public function testCurrencyValidation(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        new Currency('');
    }

    public function testCurrencyMaxLength(): void
    {
        $this->expectException(InvalidCurrencyException::class);
        new Currency('USDD');
    }
}
