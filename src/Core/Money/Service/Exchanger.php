<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Money\Service;

use Exception;
use IWD\Measurement\Core\Money\ValueObject\ExchangePrice;
use IWD\Measurement\Core\Money\ValueObject\Money;

class Exchanger
{
    public function exchange(Money $money, ExchangePrice $exchangePrice): Money
    {
        if (!$money->getCurrency()->isEqual($exchangePrice->getCurrencyPair()->getSellCurrency())) {
            throw new Exception('Sell currency and you currency is not equals');
        }
        if ($money->getCurrency()->isEqual($exchangePrice->getCurrencyPair()->getBuyCurrency())) {
            throw new Exception('It is impossible to exchange currency for an identical one');
        }

        return new Money(
            amount: $money->getAmount()->multiply($exchangePrice->getPrice()),
            currency: $exchangePrice->getCurrencyPair()->getBuyCurrency()
        );
    }
}
