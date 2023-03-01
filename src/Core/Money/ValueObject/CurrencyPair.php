<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Money\ValueObject;

use Exception;

/**
 * You can extend this ValueObject, and make Embeddable from here
 */
class CurrencyPair
{
    private readonly Currency $sellCurrency;
    private readonly Currency $buyCurrency;

    public function __construct(Currency $sellCurrency, Currency $buyCurrency)
    {
        $this->sellCurrency = $sellCurrency;
        $this->buyCurrency = $buyCurrency;
    }

    public static function fromSymbol(string $symbol, string $delimiter): self
    {
        if (!empty($delimiter)) {
            [$sellCurrency, $buyCurrency] = explode($delimiter, $symbol);

            return new self(
                sellCurrency: new Currency($sellCurrency),
                buyCurrency: new Currency($buyCurrency)
            );
        }

        throw new Exception('Invalid delimiter');
    }

    public function getSymbol(string $delimiter = '_'): string
    {
        return $this->sellCurrency->getCode() . $delimiter . $this->buyCurrency->getCode();
    }

    public function getSellCurrency(): Currency
    {
        return $this->sellCurrency;
    }

    public function getBuyCurrency(): Currency
    {
        return $this->buyCurrency;
    }
}
