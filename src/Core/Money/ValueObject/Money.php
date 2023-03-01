<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Money\ValueObject;

use Exception;
use IWD\Measurement\Core\Math\ValueObject\Measurement;

/**
 * You can extend this ValueObject, and make Embeddable from here
 */
class Money
{
    protected Measurement $amount;
    protected Currency $currency;

    public function __construct(Measurement $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function add(Money $money): Money
    {
        if ($money->amount->lessThan(new Measurement('0'))) {
            throw new Exception('Add amount less then zero');
        }
        if (!$this->currency->isEqual($money->currency)) {
            throw new Exception('Currency is not equals');
        }

        return new Money(
            amount: $this->amount->sum(
                $money->amount
            ),
            currency: $this->currency,
        );
    }

    public function subtract(Money $money): Money
    {
        if ($money->amount->lessThan(new Measurement('0'))) {
            throw new Exception('Subtract amount less then zero');
        }
        if (!$this->currency->isEqual($money->currency)) {
            throw new Exception('Currency is not equals');
        }
        if ($this->amount->lessThan($money->amount)) {
            throw new Exception('Insufficient funds');
        }

        return new Money(
            amount: $this->amount->difference(
                $money->amount
            ),
            currency: $this->currency,
        );
    }

    public function isEquals(Money $money): bool
    {
        return $this->currency->isEqual($money->currency) && $this->amount->equals($money->amount);
    }

    public function getAmount(): Measurement
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function __toString(): string
    {
        return $this->amount->getValue() . ' ' . $this->currency->getCode();
    }

    public static function fromString(string $money): self
    {
        [$amount, $currency] = explode(' ', $money);

        return new self(
            amount: new Measurement($amount),
            currency: new Currency($currency)
        );
    }
}
