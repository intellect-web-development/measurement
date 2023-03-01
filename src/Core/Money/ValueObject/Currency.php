<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Money\ValueObject;

class Currency
{
    public function __construct(
        protected readonly string $code
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isEqual(Currency $currency): bool
    {
        return $this->code === $currency->code;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
