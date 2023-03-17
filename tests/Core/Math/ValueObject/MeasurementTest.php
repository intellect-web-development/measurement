<?php

namespace Core\Math\ValueObject;

use Generator;
use IWD\Measurement\Core\Math\ValueObject\Measurement;
use PHPUnit\Framework\TestCase;

/** @covers \IWD\Measurement\Core\Math\ValueObject\Measurement */
class MeasurementTest extends TestCase
{
    public function testToString(): void
    {
        $value = random_int(-999, 999) . '.' . random_int(10, 99);
        self::assertSame(
            expected: $value,
            actual: (new Measurement($value))->__toString()
        );
    }

    public function testGetValue(): void
    {
        $value = random_int(-999, 999) . '.' . random_int(10, 99);
        self::assertSame(
            expected: $value,
            actual: (new Measurement($value))->getValue()
        );
    }

    /** @dataProvider sumProvider */
    public function testSum(Measurement $actual, Measurement $sum, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: $actual->sum($sum)->getValue()
        );
    }

    public function sumProvider(): Generator
    {
        yield 'case-1' => [
            'actual' => new Measurement('500.00'),
            'sum' => new Measurement('1000.25'),
            'expected' => new Measurement('1500.25'),
        ];
    }

    /** @dataProvider differenceProvider */
    public function testDifference(Measurement $actual, Measurement $difference, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: $actual->difference($difference)->getValue()
        );
    }

    public function differenceProvider(): Generator
    {
        yield 'case-1' => [
            'actual' => new Measurement('500.00'),
            'difference' => new Measurement('700.55'),
            'expected' => new Measurement('-200.55'),
        ];
    }

    /** @dataProvider multiplyProvider */
    public function testMultiply(Measurement $actual, array $factors, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: $actual->multiply(...$factors)->getValue()
        );
    }

    public function multiplyProvider(): Generator
    {
        yield 'case-1' => [
            'actual' => new Measurement('20.00'),
            'factors' => [
                new Measurement('0.5'),
                new Measurement('7'),
                new Measurement('-1'),
            ],
            'expected' => new Measurement('-70.00'),
        ];
    }

    /** @dataProvider divideProvider */
    public function testDivide(Measurement $actual, array $dividers, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: $actual->divide(...$dividers)?->getValue()
        );
    }

    public function divideProvider(): Generator
    {
        yield 'case-1' => [
            'actual' => new Measurement('100.00'),
            'dividers' => [
                new Measurement('0.500'),
                new Measurement('5'),
            ],
            'expected' => new Measurement('40.000'),
        ];
    }

    /** @dataProvider roundProvider */
    public function testRound(Measurement $measurement, int $precision, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: $measurement->round($precision)->getValue()
        );
    }

    public function roundProvider(): Generator
    {
        yield 'case-1' => [
            'measurement' => new Measurement('0.123456789'),
            'precision' => 5,
            'expected' => new Measurement('0.12346'),
        ];
    }

    /** @dataProvider sqrtProvider */
    public function testSqrt(Measurement $measurement, int $precision, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: $measurement->sqrt($precision)->getValue()
        );
    }

    public function sqrtProvider(): Generator
    {
        yield 'case-1' => [
            'measurement' => new Measurement('25.0000'),
            'precision' => 3,
            'expected' => new Measurement('5.000'),
        ];
    }

    /** @dataProvider powProvider */
    public function testPow(Measurement $measurement, int $exponent, int $precision, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: $measurement->pow($exponent, $precision)->getValue()
        );
    }

    public function powProvider(): Generator
    {
        yield 'case-1' => [
            'measurement' => new Measurement('100.0000'),
            'exponent' => 3,
            'precision' => 2,
            'expected' => new Measurement('1000000.00'),
        ];
    }

    public function testEquals(): void
    {
        $measurement = new Measurement('0.123456');
        self::assertTrue($measurement->equals(new Measurement('0.123457'), 5));
        self::assertFalse($measurement->equals(new Measurement('0.123457'), 6));
    }

    public function testNotEquals(): void
    {
        $measurement = new Measurement('0.123456');
        self::assertFalse($measurement->notEquals(new Measurement('0.123457'), 5));
        self::assertTrue($measurement->notEquals(new Measurement('0.123457'), 6));
    }

    public function testLessThan(): void
    {
        $measurement = new Measurement('10.0000005');
        self::assertFalse($measurement->lessThan(new Measurement('10.0000005')));
        self::assertTrue($measurement->lessThan(new Measurement('10.0000006')));
    }

    public function testGreaterThan(): void
    {
        $measurement = new Measurement('10.0000005');
        self::assertFalse($measurement->greaterThan(new Measurement('10.0000005')));
        self::assertTrue($measurement->greaterThan(new Measurement('10.0000004')));
    }

    public function testLessOrEquals(): void
    {
        $measurement = new Measurement('10.0000005');
        self::assertFalse($measurement->lessOrEquals(new Measurement('10.0000004')));
        self::assertTrue($measurement->lessOrEquals(new Measurement('10.0000005')));
    }

    public function testGreaterOrEquals(): void
    {
        $measurement = new Measurement('10.0000005');
        self::assertFalse($measurement->greaterOrEquals(new Measurement('10.0000006')));
        self::assertTrue($measurement->greaterOrEquals(new Measurement('10.0000005')));
    }
}
