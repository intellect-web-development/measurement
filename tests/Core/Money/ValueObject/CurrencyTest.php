<?php

namespace Core\Money\ValueObject;

use IWD\Measurement\Core\Money\ValueObject\Currency;
use PHPUnit\Framework\TestCase;

/** @covers \IWD\Measurement\Core\Money\ValueObject\Currency */
class CurrencyTest extends TestCase
{
    public function testConstruct(): void
    {
        $currency = new Currency('RUB');
        self::assertSame('RUB', $currency->getCode());
        self::assertSame('RUB', $currency->__toString());
        self::assertTrue($currency->isEqual(new Currency('RUB')));
        self::assertFalse($currency->isEqual(new Currency('USD')));
    }
}
