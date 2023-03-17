<?php

namespace Core\Money\ValueObject;

use IWD\Measurement\Core\Math\ValueObject\Measurement;
use IWD\Measurement\Core\Money\ValueObject\Currency;
use IWD\Measurement\Core\Money\ValueObject\Money;
use PHPUnit\Framework\TestCase;

/** @covers \IWD\Measurement\Core\Money\ValueObject\Money */
class MoneyTest extends TestCase
{
    public function testConstruct(): void
    {
        $money = new Money(
            amount: $amount = new Measurement(100),
            currency: $currency = new Currency('RUB')
        );
        self::assertSame($amount, $money->getAmount());
        self::assertSame($currency, $money->getCurrency());
    }

    public function testToString(): void
    {
        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        self::assertSame('100.00 RUB', $money->__toString());
    }

    public function testFromString(): void
    {
        $money = Money::fromString('100.00 USD');
        self::assertSame('USD', $money->getCurrency()->getCode());
        self::assertSame('100.00', $money->getAmount()->getValue());
    }

    public function testSubtractSuccess(): void
    {
        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        self::assertSame(
            '44.70',
            $money->subtract(
                new Money(
                    amount: new Measurement('55.30'),
                    currency: new Currency('RUB')
                )
            )->getAmount()->getValue()
        );
    }

    public function testSubtractFailThenSubtractAmountLessThenZero(): void
    {
        $this->expectExceptionMessage('Subtract amount less then zero');

        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        $money->subtract(
            new Money(
                amount: new Measurement('-0.000001'),
                currency: new Currency('RUB')
            )
        );
    }

    public function testSubtractFailThenCurrencyIsNotEquals(): void
    {
        $this->expectExceptionMessage('Currency is not equals');

        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        $money->subtract(
            new Money(
                amount: new Measurement(1),
                currency: new Currency('USD')
            )
        );
    }

    public function testSubtractFailThenInsufficientFunds(): void
    {
        $this->expectExceptionMessage('Insufficient funds');

        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        $money->subtract(
            new Money(
                amount: new Measurement(101),
                currency: new Currency('RUB')
            )
        );
    }

    public function testAddSuccess(): void
    {
        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        self::assertSame(
            '119.95',
            $money->add(
                new Money(
                    amount: new Measurement('19.95'),
                    currency: new Currency('RUB')
                )
            )->getAmount()->getValue()
        );
    }

    public function testAddFailThenAddAmountLessThenZero(): void
    {
        $this->expectExceptionMessage('Add amount less then zero');

        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        $money->add(
            new Money(
                amount: new Measurement('-0.000001'),
                currency: new Currency('RUB')
            )
        );
    }

    public function testAddFailThenCurrencyIsNotEquals(): void
    {
        $this->expectExceptionMessage('Currency is not equals');

        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        $money->add(
            new Money(
                amount: new Measurement(1),
                currency: new Currency('USD')
            )
        );
    }

    public function testIsEquals(): void
    {
        $money = new Money(
            amount: new Measurement(100),
            currency: new Currency('RUB')
        );
        self::assertTrue(
            $money->isEquals(
                new Money(
                    amount: new Measurement(100),
                    currency: new Currency('RUB')
                )
            )
        );
        self::assertFalse(
            $money->isEquals(
                new Money(
                    amount: new Measurement(101),
                    currency: new Currency('RUB')
                )
            )
        );
        self::assertFalse(
            $money->isEquals(
                new Money(
                    amount: new Measurement(100),
                    currency: new Currency('USD')
                )
            )
        );
        self::assertFalse(
            $money->isEquals(
                new Money(
                    amount: new Measurement(99),
                    currency: new Currency('EUR')
                )
            )
        );
    }
}
