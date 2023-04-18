<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Math\ValueObject;

use IWD\Measurement\Core\Math\Service\Math;

// todo: сделать возможность указывать точность для каждой из операций
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

    public function sum(Measurement $measurement): static
    {
        return new static(
            Math::sum($this, $measurement)->getValue(),
        );
    }

    public function difference(Measurement $measurement): static
    {
        return new static(
            Math::difference($this, $measurement)->getValue(),
        );
    }

    public function round(int $precision): static
    {
        return new static(
            Math::round($this, $precision)->getValue()
        );
    }

    public function multiply(Measurement ...$factors): static
    {
        return new static(
            Math::multiply($this, ...$factors)->getValue()
        );
    }

    public function divide(Measurement ...$dividers): static
    {
        return new static(
            Math::divide($this, ...$dividers)->getValue()
        );
    }

    public function sqrt(int $precision): static
    {
        return new static(
            Math::sqrt($this, $precision)->getValue()
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
