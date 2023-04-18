<?php

declare(strict_types=1);

namespace IWD\Measurement\Core\Math\Service;

use DivisionByZeroError;
use IWD\Measurement\Core\Math\ValueObject\Measurement;
use IWD\Measurement\Exception\MeasurementException;

class Math
{
    public static function sum(Measurement ...$terms): Measurement
    {
        $result = '0';
        foreach ($terms as $term) {
            $result = bcadd(
                $result,
                $term->getValue(),
                self::getMaxScale($result, $term->getValue())
            );
        }

        return new Measurement($result);
    }

    /**
     * @param Measurement $minuend        Уменьшаемое
     * @param Measurement ...$subtrahends Вычитаемое
     */
    public static function difference(Measurement $minuend, Measurement ...$subtrahends): Measurement
    {
        $result = $minuend->getValue();

        foreach ($subtrahends as $subtrahend) {
            $result = bcsub(
                $result,
                $subtrahend->getValue(),
                self::getMaxScale($result, $subtrahend->getValue())
            );
        }

        return new Measurement($result);
    }

    /**
     * @param Measurement $multiplicand Множимое
     * @param Measurement ...$factors   Множители
     */
    public static function multiply(Measurement $multiplicand, Measurement ...$factors): Measurement
    {
        $result = $multiplicand->getValue();

        foreach ($factors as $factor) {
            $result = bcmul(
                $result,
                $factor->getValue(),
                self::getMaxScale($result, $factor->getValue())
            );
        }

        return new Measurement($result);
    }

    public static function sqrt(Measurement $measurement, int $precision): Measurement
    {
        return new Measurement(
            bcsqrt($measurement->getValue(), $precision)
        );
    }

    public static function pow(Measurement $measurement, int $exponent, int $precision): Measurement
    {
        return new Measurement(
            bcpow($measurement->getValue(), (string) $exponent, $precision)
        );
    }

    /**
     * @param Measurement $dividend Делимое
     * @param Measurement ...$dividers Делитель
     * @throws MeasurementException
     */
    public static function divide(Measurement $dividend, Measurement ...$dividers): Measurement
    {
        $result = $dividend->getValue();

        try {
            foreach ($dividers as $divider) {
                $result = bcdiv(
                    $result,
                    $divider->getValue(),
                    self::getMaxScale($result, $divider->getValue())
                );
            }
        } catch (DivisionByZeroError $exception) {
            throw new MeasurementException('Division by zero');
        }

        return new Measurement($result);
    }

    public static function abs(Measurement $measurement): Measurement
    {
        return new Measurement(ltrim($measurement->getValue(), '-'));
    }

    public static function max(Measurement ...$measurements): Measurement
    {
        $values = [];
        foreach ($measurements as $measurement) {
            $values[] = $measurement->getValue();
        }

        $max = max($values);

        return new Measurement(!empty($max) ? $max : 0);
    }

    public static function min(Measurement ...$measurements): Measurement
    {
        $values = [];
        foreach ($measurements as $measurement) {
            $values[] = $measurement->getValue();
        }

        $min = min($values);

        return new Measurement(!empty($min) ? $min : 0);
    }

    public static function avg(Measurement ...$measurements): Measurement
    {
        $values = [];
        foreach ($measurements as $measurement) {
            $values[] = $measurement->getValue();
        }
        if (empty($values)) {
            throw new MeasurementException('Impossible to get average from emptiness');
        }

        return self::divide(
            new Measurement((string) array_sum($values)),
            new Measurement((string) count($values)),
        );
    }

    public static function round(Measurement $measurement, int $precision): Measurement
    {
        return new Measurement(
            Math::bcround(
                $measurement->getValue(),
                $precision
            )
        );
    }

    public static function equals(Measurement $leftOperand, Measurement $rightOperand, ?int $precision = null): bool
    {
        return 0 === bccomp(
            $leftOperand->getValue(),
            $rightOperand->getValue(),
            null !== $precision ? $precision : self::getMaxScale($leftOperand->getValue(), $rightOperand->getValue())
        );
    }

    public static function notEquals(Measurement $leftOperand, Measurement $rightOperand, ?int $precision = null): bool
    {
        return 0 !== bccomp(
            $leftOperand->getValue(),
            $rightOperand->getValue(),
            null !== $precision ? $precision : self::getMaxScale($leftOperand->getValue(), $rightOperand->getValue())
        );
    }

    public static function lessThan(Measurement $leftOperand, Measurement $rightOperand, ?int $precision = null): bool
    {
        return -1 === bccomp(
            $leftOperand->getValue(),
            $rightOperand->getValue(),
            null !== $precision ? $precision : self::getMaxScale($leftOperand->getValue(), $rightOperand->getValue())
        );
    }

    public static function greaterThan(Measurement $leftOperand, Measurement $rightOperand, ?int $precision = null): bool
    {
        return 1 === bccomp(
            $leftOperand->getValue(),
            $rightOperand->getValue(),
            null !== $precision ? $precision : self::getMaxScale($leftOperand->getValue(), $rightOperand->getValue())
        );
    }

    public static function lessOrEquals(Measurement $leftOperand, Measurement $rightOperand, ?int $precision = null): bool
    {
        return 1 !== bccomp(
            $leftOperand->getValue(),
            $rightOperand->getValue(),
            null !== $precision ? $precision : self::getMaxScale($leftOperand->getValue(), $rightOperand->getValue())
        );
    }

    public static function greaterOrEquals(Measurement $leftOperand, Measurement $rightOperand, ?int $precision = null): bool
    {
        return -1 !== bccomp(
            $leftOperand->getValue(),
            $rightOperand->getValue(),
            null !== $precision ? $precision : self::getMaxScale($leftOperand->getValue(), $rightOperand->getValue())
        );
    }

    private static function getMaxScale(string $leftOperand, string $rightOperand): int
    {
        $leftExplode = explode('.', $leftOperand);
        $rightExplode = explode('.', $rightOperand);

        $leftScale = isset($leftExplode[1]) ? strlen($leftExplode[1]) : 0;
        $rightScale = isset($rightExplode[1]) ? strlen($rightExplode[1]) : 0;

        return max($leftScale, $rightScale);
    }

    private static function bcround(string $number, int $precision): string
    {
        if ($precision >= 0) {
            if (str_contains($number, '.')) {
                if ('-' != $number[0]) {
                    return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
                }

                return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            }

            return $number;
        } else {
            $pow = bcpow('10', (string) -$precision);

            return bcmul(self::bcround(bcdiv($number, $pow, -$precision), 0), $pow);
        }
    }
}
