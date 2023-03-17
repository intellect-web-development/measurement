<?php

declare(strict_types=1);

namespace Core\Money\Service;

use Generator;
use IWD\Measurement\Core\Math\ValueObject\Measurement;
use IWD\Measurement\Core\Money\Service\Exchanger;
use IWD\Measurement\Core\Money\ValueObject\Currency;
use IWD\Measurement\Core\Money\ValueObject\CurrencyPair;
use IWD\Measurement\Core\Money\ValueObject\ExchangePrice;
use IWD\Measurement\Core\Money\ValueObject\Money;
use PHPUnit\Framework\TestCase;

/** @covers \IWD\Measurement\Core\Money\Service\Exchanger */
class ExchangerTest extends TestCase
{
    /** @dataProvider successDataProvider */
    public function testExchange(
        Measurement $amount,
        Measurement $price,
        Measurement $expected,
        Currency $sellCurrency,
        Currency $buyCurrency,
        int $precision,
    ): void {
        $sellMoney = new Money(
            amount: $amount,
            currency: $sellCurrency
        );
        $exchangePrice = new ExchangePrice(
            currencyPair: new CurrencyPair(
                sellCurrency: $sellCurrency,
                buyCurrency: $buyCurrency,
            ),
            price: $price
        );

        $exchanger = new Exchanger();
        $buyMoney = $exchanger->exchange(
            $sellMoney,
            $exchangePrice
        );
        self::assertSame(
            expected: $expected->round($precision)->getValue(),
            actual: $buyMoney->getAmount()->round($precision)->getValue()
        );
        self::assertNotEquals($sellMoney->getCurrency()->getCode(), $buyMoney->getCurrency()->getCode());
        self::assertSame($buyCurrency->getCode(), $buyMoney->getCurrency()->getCode());
    }

    public function successDataProvider(): Generator
    {
        $precision = 10;

        yield 'USD_RUB' => [
            'amount' => new Measurement(100),
            'price' => new Measurement('69.6481'),
            'expected' => new Measurement('6964.8100'),
            'sellCurrency' => new Currency('USD'),
            'buyCurrency' => new Currency('RUB'),
            'precision' => $precision,
        ];
        yield 'RUB_USD' => [
            'amount' => new Measurement(500),
            'price' => new Measurement('0.014312'),
            'expected' => new Measurement('7.156'),
            'sellCurrency' => new Currency('RUB'),
            'buyCurrency' => new Currency('USD'),
            'precision' => $precision,
        ];
        yield 'BTC_USDT' => [
            'amount' => new Measurement('0.001'),
            'price' => new Measurement('17424.44'),
            'expected' => new Measurement('17.424'),
            'sellCurrency' => new Currency('BTC'),
            'buyCurrency' => new Currency('USDT'),
            'precision' => $precision,
        ];
        yield 'USDT_BTC' => [
            'amount' => new Measurement(100),
            'price' => new Measurement('0.000057'),
            'expected' => new Measurement('0.0057'),
            'sellCurrency' => new Currency('USDT'),
            'buyCurrency' => new Currency('BTC'),
            'precision' => $precision,
        ];
        yield 'TRX_USDT' => [
            'amount' => new Measurement(10000),
            'price' => new Measurement('0.055'),
            'expected' => new Measurement(550),
            'sellCurrency' => new Currency('TRX'),
            'buyCurrency' => new Currency('USDT'),
            'precision' => $precision,
        ];
    }

    public function testExchangeThenExchangeCurrencyForAnIdenticalOne(): void
    {
        $this->expectExceptionMessage('It is impossible to exchange currency for an identical one');

        $exchanger = new Exchanger();
        $exchanger->exchange(
            money: new Money(
                amount: new Measurement('100'),
                currency: new Currency('USD')
            ),
            exchangePrice: new ExchangePrice(
                currencyPair: new CurrencyPair(
                    sellCurrency: new Currency('USD'),
                    buyCurrency: new Currency('USD'),
                ),
                price: new Measurement('50')
            )
        );
    }

    public function testExchangeThenSellCurrencyAndYouCurrencyIsNotEquals(): void
    {
        $this->expectExceptionMessage('Sell currency and you currency is not equals');

        $exchanger = new Exchanger();
        $exchanger->exchange(
            money: new Money(
                amount: new Measurement('100'),
                currency: new Currency('USD')
            ),
            exchangePrice: new ExchangePrice(
                currencyPair: new CurrencyPair(
                    sellCurrency: new Currency('RUB'),
                    buyCurrency: new Currency('USD'),
                ),
                price: new Measurement('50')
            )
        );
    }
}
