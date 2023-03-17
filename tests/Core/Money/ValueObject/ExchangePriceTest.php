<?php

namespace Core\Money\ValueObject;

use IWD\Measurement\Core\Math\ValueObject\Measurement;
use IWD\Measurement\Core\Money\ValueObject\Currency;
use IWD\Measurement\Core\Money\ValueObject\CurrencyPair;
use IWD\Measurement\Core\Money\ValueObject\ExchangePrice;
use PHPUnit\Framework\TestCase;

/** @covers \IWD\Measurement\Core\Money\ValueObject\ExchangePrice */
class ExchangePriceTest extends TestCase
{
    public function testConstruct(): void
    {
        $exchangePrice = new ExchangePrice(
            currencyPair: $currencyPair = new CurrencyPair(
                sellCurrency: new Currency('BTC'),
                buyCurrency: new Currency('USDT'),
            ),
            price: $price = new Measurement('0.99')
        );
        self::assertSame($price, $exchangePrice->getPrice());
        self::assertSame($currencyPair, $exchangePrice->getCurrencyPair());
    }

    public function testDeserialize(): void
    {
        $exchangePrice = ExchangePrice::deserialize('USD_RUB,1.05');
        self::assertSame('1.05', $exchangePrice->getPrice()->getValue());
        self::assertSame('USD', $exchangePrice->getCurrencyPair()->getSellCurrency()->getCode());
        self::assertSame('RUB', $exchangePrice->getCurrencyPair()->getBuyCurrency()->getCode());
    }

    public function testSerialize(): void
    {
        $exchangePrice = new ExchangePrice(
            currencyPair: new CurrencyPair(
                sellCurrency: new Currency('EUR'),
                buyCurrency: new Currency('USD'),
            ),
            price: new Measurement('1.05')
        );
        self::assertSame('EUR_USD,1.05', $exchangePrice->serialize());
    }
}