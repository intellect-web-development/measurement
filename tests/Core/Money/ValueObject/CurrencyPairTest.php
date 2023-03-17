<?php

namespace Core\Money\ValueObject;

use IWD\Measurement\Core\Money\ValueObject\Currency;
use IWD\Measurement\Core\Money\ValueObject\CurrencyPair;
use PHPUnit\Framework\TestCase;

/** @covers \IWD\Measurement\Core\Money\ValueObject\CurrencyPair */
class CurrencyPairTest extends TestCase
{
    public function testConstruct(): void
    {
        $currencyPair = new CurrencyPair(
            sellCurrency: $sellCurrency = new Currency('BTC'),
            buyCurrency: $buyCurrency = new Currency('USDT'),
        );
        self::assertSame('BTC_USDT', $currencyPair->getSymbol('_'));
        self::assertSame('BTCUSDT', $currencyPair->getSymbol(''));
        self::assertSame($sellCurrency, $currencyPair->getSellCurrency());
        self::assertSame($buyCurrency, $currencyPair->getBuyCurrency());
    }

    public function testFromSymbolThenEmptyDelimiter(): void
    {
        $this->expectExceptionMessage('Invalid delimiter');
        CurrencyPair::fromSymbol('BTCUSD', '');
    }

    public function testFromSymbolThenSuccess(): void
    {
        $currencyPair = CurrencyPair::fromSymbol('BTC_USD', '_');
        self::assertSame('BTC', $currencyPair->getSellCurrency()->getCode());
        self::assertSame('USD', $currencyPair->getBuyCurrency()->getCode());
    }
}
