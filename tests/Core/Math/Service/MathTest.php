<?php

namespace Core\Math\Service;

use Generator;
use IWD\Measurement\Core\Math\Service\Math;
use IWD\Measurement\Core\Math\ValueObject\Measurement;
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    /** @dataProvider sumProvider */
    public function testSum(array $terms, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::sum(...$terms)->getValue()
        );
    }

    public function sumProvider(): Generator
    {
        yield 'thousand' => [
            'terms' => [
                new Measurement('500.00'),
                new Measurement('499.99'),
                new Measurement('0.01'),
            ],
            'expected' => new Measurement('1000.00'),
        ];
        yield 'hundred' => [
            'terms' => [
                new Measurement('12.945'),
                new Measurement('51.99'),
                new Measurement('0.01'),
                new Measurement('0.01252'),
                new Measurement('32.501953'),
                new Measurement('2.25921'),
                new Measurement('0.281317'),
            ],
            'expected' => new Measurement('100.000000'),
        ];
        yield 'incomplete-hundred' => [
            'terms' => [
                new Measurement('12.945'),
                new Measurement('51.99'),
                new Measurement('0.01'),
                new Measurement('0.01252'),
                new Measurement('32.501953'),
                new Measurement('2.25921'),
            ],
            'expected' => new Measurement('99.718683'),
        ];
    }

    /** @dataProvider multiplyProvider */
    public function testMultiply(Measurement $multiplicand, array $subtrahends, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::multiply($multiplicand, ...$subtrahends)->getValue()
        );
    }

    public function multiplyProvider(): Generator
    {
        yield 'case-1' => [
            'multiplicand' => new Measurement('2'),
            'factors' => [
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2'),
            ],
            'expected' => new Measurement('1024'),
        ];
        yield 'case-2' => [
            'multiplicand' => new Measurement('2'),
            'factors' => [
                new Measurement('2'),
                new Measurement('2'),
                new Measurement('2.00'),
                new Measurement('2'),
                new Measurement('2'),
            ],
            'expected' => new Measurement('64.00'),
        ];
        yield 'case-3' => [
            'multiplicand' => new Measurement('15'),
            'factors' => [
                new Measurement('0.0001'),
                new Measurement('2.00000'),
            ],
            'expected' => new Measurement('0.00300'),
        ];
        yield 'case-4' => [
            'multiplicand' => new Measurement('100'),
            'factors' => [
                new Measurement('-1'),
                new Measurement('-1.000'),
            ],
            'expected' => new Measurement('100.000'),
        ];
    }

    /** @dataProvider differenceProvider */
    public function testDifference(Measurement $minuend, array $subtrahends, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::difference($minuend, ...$subtrahends)->getValue()
        );
    }

    public function differenceProvider(): Generator
    {
        yield 'case-1' => [
            'minuend' => new Measurement('1000.00'),
            'subtrahends' => [
                new Measurement('498.99'),
                new Measurement('1.00'),
                new Measurement('0.01'),
            ],
            'expected' => new Measurement('500.00'),
        ];
        yield 'case-2' => [
            'minuend' => new Measurement('-1000.00'),
            'subtrahends' => [
                new Measurement('498.99'),
                new Measurement('1.00'),
                new Measurement('0.01'),
            ],
            'expected' => new Measurement('-1500.00'),
        ];
        yield 'case-3' => [
            'minuend' => new Measurement('1000.00'),
            'subtrahends' => [
                new Measurement('999.99999999999'),
            ],
            'expected' => new Measurement('0.00000000001'),
        ];
        yield 'case-4' => [
            'minuend' => new Measurement('1'),
            'subtrahends' => [
                new Measurement('0.2222220'),
                new Measurement('0.0000001'),
                new Measurement('0.0000001'),
                new Measurement('0.0000003'),
            ],
            'expected' => new Measurement('0.7777775'),
        ];
    }

    /** @dataProvider divideProvider */
    public function testDivide(Measurement $dividend, array $dividers, ?Measurement $expected, int $precision = 0): void
    {
        if (null === $expected) {
            self::assertNull(Math::divide($dividend, ...$dividers));
        } else {
            self::assertSame(
                expected: $expected->getValue(),
                actual: Math::divide($dividend, ...$dividers)?->getValue()
            );
        }
    }

    public function divideProvider(): Generator
    {
        yield 'case-1' => [
            'dividend' => new Measurement('1000.00'),
            'dividers' => [
                new Measurement('10.000'),
                new Measurement('10.000'),
            ],
            'expected' => new Measurement('10.000'),
        ];
        yield 'case-2' => [
            'dividend' => new Measurement('1000.00'),
            'dividers' => [
                new Measurement('10.000'),
                new Measurement('0.00'),
                new Measurement('10.000'),
            ],
            'expected' => null,
        ];
        yield 'case-3' => [
            'dividend' => new Measurement('1000.00'),
            'dividers' => [
                new Measurement('3'),
            ],
            'expected' => new Measurement('333.33'),
        ];
        yield 'case-4' => [
            'dividend' => new Measurement('1000.00'),
            'dividers' => [
                new Measurement('3.000'),
            ],
            'expected' => new Measurement('333.333'),
        ];
        yield 'case-5' => [
            'dividend' => new Measurement('1000'),
            'dividers' => [
                new Measurement('3'),
            ],
            'expected' => new Measurement('333.33'),
        ];
    }

    /** @dataProvider sqrtProvider */
    public function testSqrt(Measurement $actual, int $precision, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::sqrt($actual, $precision)->getValue()
        );
    }

    public function sqrtProvider(): Generator
    {
        yield 'case-1' => [
            'actual' => new Measurement('25'),
            'precision' => 0,
            'expected' => new Measurement('5'),
        ];
        yield 'case-2' => [
            'actual' => new Measurement('25'),
            'precision' => 5,
            'expected' => new Measurement('5.00000'),
        ];
        yield 'case-3' => [
            'actual' => new Measurement('5'),
            'precision' => 10,
            'expected' => new Measurement('2.2360679774'),
        ];
    }

    /** @dataProvider absProvider */
    public function testAbs(Measurement $actual, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::abs($actual)->getValue()
        );
    }

    public function absProvider(): Generator
    {
        yield 'case-1' => [
            'actual' => new Measurement('0'),
            'expected' => new Measurement('0'),
        ];
        yield 'case-2' => [
            'actual' => new Measurement('25.00'),
            'expected' => new Measurement('25.00'),
        ];
        yield 'case-3' => [
            'actual' => new Measurement('-25.00'),
            'expected' => new Measurement('25.00'),
        ];
        yield 'case-4' => [
            'actual' => new Measurement('125'),
            'expected' => new Measurement('125'),
        ];
        yield 'case-5' => [
            'actual' => new Measurement('-125'),
            'expected' => new Measurement('125'),
        ];
    }

    /** @dataProvider maxProvider */
    public function testMax(array $scope, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::max(...$scope)->getValue()
        );
    }

    public function maxProvider(): Generator
    {
        yield 'case-1' => [
            'scope' => [
                new Measurement('15'),
            ],
            'expected' => new Measurement('15'),
        ];
        yield 'case-2' => [
            'scope' => [
                new Measurement('20.00'),
                new Measurement('7.00'),
                new Measurement('-1000.000'),
                new Measurement('5000.000'),
                new Measurement('1000.000'),
            ],
            'expected' => new Measurement('5000.000'),
        ];
    }

    /** @dataProvider minProvider */
    public function testMin(array $scope, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::min(...$scope)->getValue()
        );
    }

    public function minProvider(): Generator
    {
        yield 'case-0' => [
            'scope' => [
                new Measurement('0'),
            ],
            'expected' => new Measurement('0'),
        ];
        yield 'case-1' => [
            'scope' => [
                new Measurement('10'),
            ],
            'expected' => new Measurement('10'),
        ];
        yield 'case-2' => [
            'scope' => [
                new Measurement('20.00'),
                new Measurement('7.00'),
                new Measurement('-1000.000'),
                new Measurement('5000.000'),
                new Measurement('1000.000'),
            ],
            'expected' => new Measurement('-1000.000'),
        ];
    }

    /** @dataProvider equalsProvider */
    public function testEquals(Measurement $left, Measurement $right, ?int $precision, bool $expected): void
    {
        self::assertSame(
            expected: $expected,
            actual: Math::equals($left, $right, $precision)
        );
    }

    public function equalsProvider(): Generator
    {
        yield 'case-1' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-2' => [
            'left' => new Measurement('-10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => false,
        ];
        yield 'case-3' => [
            'left' => new Measurement('10.1'),
            'right' => new Measurement('10'),
            'precision' => 0,
            'expected' => true,
        ];
        yield 'case-4' => [
            'left' => new Measurement('10.1'),
            'right' => new Measurement('10'),
            'precision' => 1,
            'expected' => false,
        ];
        yield 'case-5' => [
            'left' => new Measurement('10.0000005'),
            'right' => new Measurement('10.00000055'),
            'precision' => null,
            'expected' => false,
        ];
        yield 'case-6' => [
            'left' => new Measurement('10.0000005'),
            'right' => new Measurement('10.00000055'),
            'precision' => 7,
            'expected' => true,
        ];
        yield 'case-7' => [
            'left' => new Measurement('10.0000005'),
            'right' => new Measurement('10.00000055'),
            'precision' => 8,
            'expected' => false,
        ];
    }

    /** @dataProvider notEqualsProvider */
    public function testNotEquals(Measurement $left, Measurement $right, ?int $precision, bool $expected): void
    {
        self::assertSame(
            expected: $expected,
            actual: Math::notEquals($left, $right, $precision)
        );
    }

    public function notEqualsProvider(): Generator
    {
        yield 'case-1' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => false,
        ];
        yield 'case-2' => [
            'left' => new Measurement('-10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-3' => [
            'left' => new Measurement('10.1'),
            'right' => new Measurement('10'),
            'precision' => 0,
            'expected' => false,
        ];
        yield 'case-4' => [
            'left' => new Measurement('10.1'),
            'right' => new Measurement('10'),
            'precision' => 1,
            'expected' => true,
        ];
        yield 'case-5' => [
            'left' => new Measurement('10.0000005'),
            'right' => new Measurement('10.00000055'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-6' => [
            'left' => new Measurement('10.0000005'),
            'right' => new Measurement('10.00000055'),
            'precision' => 7,
            'expected' => false,
        ];
        yield 'case-7' => [
            'left' => new Measurement('10.0000005'),
            'right' => new Measurement('10.00000055'),
            'precision' => 8,
            'expected' => true,
        ];
    }

    /** @dataProvider lessThanProvider */
    public function testLessThan(Measurement $left, Measurement $right, ?int $precision, bool $expected): void
    {
        self::assertSame(
            expected: $expected,
            actual: Math::lessThan($left, $right, $precision)
        );
    }

    public function lessThanProvider(): Generator
    {
        yield 'case-1' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => false,
        ];
        yield 'case-2' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10.00000001'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-3' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10.0001'),
            'precision' => 4,
            'expected' => true,
        ];
        yield 'case-4' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10.0001'),
            'precision' => 3,
            'expected' => false,
        ];
    }

    /** @dataProvider lessOrEqualsProvider */
    public function testLessOrEquals(Measurement $left, Measurement $right, ?int $precision, bool $expected): void
    {
        self::assertSame(
            expected: $expected,
            actual: Math::lessOrEquals($left, $right, $precision)
        );
    }

    public function lessOrEqualsProvider(): Generator
    {
        yield 'case-1' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-2' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10.00000001'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-3' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10.0001'),
            'precision' => 4,
            'expected' => true,
        ];
        yield 'case-4' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10.0001'),
            'precision' => 3,
            'expected' => true,
        ];
        yield 'case-5' => [
            'left' => new Measurement('10.0002'),
            'right' => new Measurement('10.0001'),
            'precision' => 3,
            'expected' => true,
        ];
        yield 'case-6' => [
            'left' => new Measurement('10.0002'),
            'right' => new Measurement('10.0001'),
            'precision' => 4,
            'expected' => false,
        ];
    }

    /** @dataProvider greaterThanProvider */
    public function testGreaterThan(Measurement $left, Measurement $right, ?int $precision, bool $expected): void
    {
        self::assertSame(
            expected: $expected,
            actual: Math::greaterThan($left, $right, $precision)
        );
    }

    public function greaterThanProvider(): Generator
    {
        yield 'case-1' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => false,
        ];
        yield 'case-2' => [
            'left' => new Measurement('10.00000001'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-3' => [
            'left' => new Measurement('10.0001'),
            'right' => new Measurement('10'),
            'precision' => 4,
            'expected' => true,
        ];
        yield 'case-4' => [
            'left' => new Measurement('10.0001'),
            'right' => new Measurement('10'),
            'precision' => 3,
            'expected' => false,
        ];
    }

    /** @dataProvider greaterOrEqualsProvider */
    public function testGreaterOrEquals(Measurement $left, Measurement $right, ?int $precision, bool $expected): void
    {
        self::assertSame(
            expected: $expected,
            actual: Math::greaterOrEquals($left, $right, $precision)
        );
    }

    public function greaterOrEqualsProvider(): Generator
    {
        yield 'case-1' => [
            'left' => new Measurement('10'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-2' => [
            'left' => new Measurement('10.00000001'),
            'right' => new Measurement('10'),
            'precision' => null,
            'expected' => true,
        ];
        yield 'case-3' => [
            'left' => new Measurement('10.0001'),
            'right' => new Measurement('10'),
            'precision' => 4,
            'expected' => true,
        ];
        yield 'case-4' => [
            'left' => new Measurement('10.0001'),
            'right' => new Measurement('10'),
            'precision' => 3,
            'expected' => true,
        ];
        yield 'case-5' => [
            'left' => new Measurement('10.0001'),
            'right' => new Measurement('10.0002'),
            'precision' => 3,
            'expected' => true,
        ];
        yield 'case-6' => [
            'left' => new Measurement('10.0001'),
            'right' => new Measurement('10.0002'),
            'precision' => 4,
            'expected' => false,
        ];
    }

    /** @dataProvider avgProvider */
    public function testAvg(array $scope, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::avg(...$scope)?->getValue()
        );
    }

    public function testAvgThenEmpty(): void
    {
        self::assertNull(
            actual: Math::avg()
        );
    }

    public function avgProvider(): Generator
    {
        yield 'case-1' => [
            'scope' => [
                new Measurement('10'),
            ],
            'expected' => new Measurement('10'),
        ];
        yield 'case-2' => [
            'scope' => [
                new Measurement('0'),
                new Measurement('5000'),
            ],
            'expected' => new Measurement('2500'),
        ];
        yield 'case-3' => [
            'scope' => [
                new Measurement('1000'),
                new Measurement('2000'),
                new Measurement('10000'),
            ],
            'expected' => new Measurement('4333.33'),
        ];
        yield 'case-4' => [
            'scope' => [
                new Measurement('333.222'),
                new Measurement('222.111'),
                new Measurement('111.8'),
            ],
            'expected' => new Measurement('222.377'),
        ];
        yield 'case-5' => [
            'scope' => [
                new Measurement('333.222'),
                new Measurement('222.111'),
                new Measurement('111.8'),
                new Measurement('-5000.007'),
            ],
            'expected' => new Measurement('-1083.218'),
        ];
    }

    /** @dataProvider powProvider */
    public function testPow(Measurement $actual, int $exponent, int $precision, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::pow($actual, $exponent, $precision)->getValue()
        );
    }

    public function powProvider(): Generator
    {
        yield 'case-1' => [
            'actual' => new Measurement('5'),
            'exponent' => 2,
            'precision' => 1,
            'expected' => new Measurement('25.0'),
        ];
        yield 'case-2' => [
            'actual' => new Measurement('2'),
            'exponent' => 10,
            'precision' => 0,
            'expected' => new Measurement('1024'),
        ];
        yield 'case-3' => [
            'actual' => new Measurement('5.00'),
            'exponent' => 3,
            'precision' => 3,
            'expected' => new Measurement('125.000'),
        ];
        yield 'case-4' => [
            'actual' => new Measurement('-5.00'),
            'exponent' => 3,
            'precision' => 3,
            'expected' => new Measurement('-125.000'),
        ];
        yield 'case-5' => [
            'actual' => new Measurement('-5.00'),
            'exponent' => 2,
            'precision' => 4,
            'expected' => new Measurement('25.0000'),
        ];
    }

    /** @dataProvider roundProvider */
    public function testRound(Measurement $measurement, int $precision, Measurement $expected): void
    {
        self::assertSame(
            expected: $expected->getValue(),
            actual: Math::round($measurement, $precision)->getValue()
        );
    }

    public function roundProvider(): Generator
    {
        $measurement = new Measurement('500.123456789');
        yield 'case-1' => [
            'measurement' => $measurement,
            'precision' => 2,
            'expected' => new Measurement('500.12'),
        ];
        yield 'case-2' => [
            'measurement' => $measurement,
            'precision' => 3,
            'expected' => new Measurement('500.123'),
        ];
        yield 'case-3' => [
            'measurement' => $measurement,
            'precision' => 5,
            'expected' => new Measurement('500.12346'),
        ];
        yield 'case-4' => [
            'measurement' => $measurement,
            'precision' => 9,
            'expected' => new Measurement('500.123456789'),
        ];
        yield 'case-5' => [
            'measurement' => $measurement,
            'precision' => 10,
            'expected' => new Measurement('500.1234567890'),
        ];
        yield 'case-6' => [
            'measurement' => $measurement,
            'precision' => 0,
            'expected' => new Measurement('500'),
        ];
        yield 'case-7' => [
            'measurement' => $measurement,
            'precision' => -1,
            'expected' => new Measurement('500'),
        ];
        yield 'case-9' => [
            'measurement' => $measurement,
            'precision' => -2,
            'expected' => new Measurement('500'),
        ];
        yield 'case-10' => [
            'measurement' => $measurement,
            'precision' => -3,
            'expected' => new Measurement('1000'),
        ];
        yield 'case-11' => [
            'measurement' => $measurement,
            'precision' => -4,
            'expected' => new Measurement('0'),
        ];
        yield 'case-12' => [
            'measurement' => new Measurement('-500.123456789'),
            'precision' => 3,
            'expected' => new Measurement('-500.123'),
        ];
        yield 'case-13' => [
            'measurement' => new Measurement('-500'),
            'precision' => 4,
            'expected' => new Measurement('-500.0000'),
        ];
        yield 'case-14' => [
            'measurement' => new Measurement('500'),
            'precision' => 3,
            'expected' => new Measurement('500.000'),
        ];
    }
}
