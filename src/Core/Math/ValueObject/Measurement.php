<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Math\ValueObject;

use IWD\Measurement\Core\Math\Service\Math;
use IWD\Measurement\Exception\MeasurementException;

/** @phpstan-consistent-constructor */
class Measurement
{
    protected readonly string $value;

    public function __construct(
        string|int $value
    ) {
        $this->value = str_contains((string) $value, '.')
            ? (string) $value
            : ((string) $value) . '.00'
        ;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toInteger(int $precision): int
    {
        return (int) $this->value;
    }

    public function toFloat(int $precision): float
    {
        return (float) $this->value;
    }

    public function toStringInteger(int $precision): string
    {
        return (string) (int) $this->value;
    }

    public function increment(): static
    {
        return new static(
            Math::sum(terms: [$this, new Measurement(1)])->getValue(),
        );
    }

    public function decrement(): static
    {
        return new static(
            Math::difference(minuend: $this, subtrahends:[new Measurement(1)])->getValue(),
        );
    }

    public function sum(Measurement $measurement, ?int $precision = null): static
    {
        return new static(
            Math::sum(terms: [$this, $measurement], precision: $precision)->getValue(),
        );
    }

    public function difference(Measurement $measurement, ?int $precision = null): static
    {
        return new static(
            Math::difference(minuend: $this, subtrahends: [$measurement], precision: $precision)->getValue(),
        );
    }

    public function round(int $precision): static
    {
        return new static(
            Math::round(measurement: $this, precision: $precision)->getValue()
        );
    }

    /**
     * @param Measurement[] $factors
     */
    public function multiply(array $factors, ?int $precision = null): static
    {
        return new static(
            Math::multiply(multiplicand: $this, factors: $factors, precision: $precision)->getValue()
        );
    }

    /**
     * @param Measurement[] $dividers
     * @throws MeasurementException
     */
    public function divide(array $dividers, ?int $precision = null): static
    {
        return new static(
            Math::divide(dividend: $this, dividers: $dividers, precision: $precision)->getValue()
        );
    }

    public function sqrt(int $precision): static
    {
        return new static(
            Math::sqrt(measurement: $this, precision: $precision)->getValue()
        );
    }

    public function pow(int $exponent, int $precision): static
    {
        return new static(
            Math::pow(
                measurement: $this,
                exponent: $exponent,
                precision: $precision
            )->getValue()
        );
    }

    public function equals(Measurement $measurement, ?int $precision = null): bool
    {
        return Math::equals(
            leftOperand: $this,
            rightOperand: $measurement,
            precision: $precision,
        );
    }

    public function notEquals(Measurement $measurement, ?int $precision = null): bool
    {
        return Math::notEquals(
            leftOperand: $this,
            rightOperand: $measurement,
            precision: $precision,
        );
    }

    public function lessThan(Measurement $measurement, ?int $precision = null): bool
    {
        return Math::lessThan(
            leftOperand: $this,
            rightOperand: $measurement,
            precision: $precision,
        );
    }

    public function greaterThan(Measurement $measurement, ?int $precision = null): bool
    {
        return Math::greaterThan(
            leftOperand: $this,
            rightOperand: $measurement,
            precision: $precision,
        );
    }

    public function lessOrEquals(Measurement $measurement, ?int $precision = null): bool
    {
        return Math::lessOrEquals(
            leftOperand: $this,
            rightOperand: $measurement,
            precision: $precision,
        );
    }

    public function greaterOrEquals(Measurement $measurement, ?int $precision = null): bool
    {
        return Math::greaterOrEquals(
            leftOperand: $this,
            rightOperand: $measurement,
            precision: $precision,
        );
    }
}
