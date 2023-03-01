<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Money\ValueObject;

use IWD\Measurement\Core\Math\ValueObject\Measurement;

/**
 * You can extend this ValueObject, and make Embeddable from here
 */
class ExchangePrice
{
    private readonly CurrencyPair $currencyPair;
    private readonly Measurement $price;

    public function __construct(CurrencyPair $currencyPair, Measurement $price)
    {
        $this->currencyPair = $currencyPair;
        $this->price = $price;
    }

    public static function deserialize(string $exchangePrice): self
    {
        [$symbol, $price] = explode(',', $exchangePrice);

        return new ExchangePrice(
            CurrencyPair::fromSymbol($symbol, '_'),
            new Measurement($price)
        );
    }

    public function serialize(): string
    {
        return $this->currencyPair->getSymbol('_') . ',' . $this->price->getValue();
    }

    public function getCurrencyPair(): CurrencyPair
    {
        return $this->currencyPair;
    }

    public function getPrice(): Measurement
    {
        return $this->price;
    }
}
