<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Exceptions\Internationalization\InvalidCurrencyCodeException;
use Cline\ValueObjects\Money;

describe('Money', function (): void {
    describe('Happy Paths', function (): void {
        describe('Creation', function (): void {
            test('creates from major units with various formats', function (): void {
                expect(Money::createFromMajorUnits(100, 'USD')->toString())->toEqual('USD 100.00');
                expect(Money::createFromMajorUnits(100.00, 'USD')->toString())->toEqual('USD 100.00');
                expect(Money::createFromMajorUnits('100', 'USD')->toString())->toEqual('USD 100.00');
            });

            test('creates from minor units with various formats', function (): void {
                expect(Money::createFromMinorUnits(100, 'USD')->toString())->toEqual('USD 1.00');
                expect(Money::createFromMinorUnits(100.00, 'USD')->toString())->toEqual('USD 1.00');
                expect(Money::createFromMinorUnits('100', 'USD')->toString())->toEqual('USD 1.00');
            });

            test('creates zero money', function (): void {
                $zero = Money::createZero('USD');
                expect($zero->isZero())->toBeTrue();
                expect($zero->toString())->toEqual('USD 0.00');

                $defaultZero = Money::createZero();
                expect($defaultZero->toString())->toEqual('EUR 0.00');
            });

            test('creates from array serialization', function (): void {
                $fromArray = Money::createFromArray([
                    'amount_in_minor_units' => 10_050,
                    'amount_in_major_units' => 100.50,
                    'currency' => 'USD',
                ]);
                expect($fromArray->toString())->toEqual('USD 100.50');
            });

            test('validates ISO 4217 currency codes', function (): void {
                $usd = Money::createFromMajorUnits(100, 'USD');
                $eur = Money::createFromMajorUnits(100, 'EUR');
                $sek = Money::createFromMajorUnits(100, 'SEK');

                expect($usd->getCurrency())->toEqual('USD');
                expect($eur->getCurrency())->toEqual('EUR');
                expect($sek->getCurrency())->toEqual('SEK');
            });
        });

        describe('Formatting', function (): void {
            test('formats from major units', function (): void {
                expect(Money::createFromMajorUnits(100, 'USD')->format('en_US'))->toEqual('$100.00');
                expect(Money::createFromMajorUnits(100.00, 'USD')->format('en_US'))->toEqual('$100.00');
                expect(Money::createFromMajorUnits('100', 'USD')->format('en_US'))->toEqual('$100.00');
            });

            test('formats from minor units', function (): void {
                expect(Money::createFromMinorUnits(100, 'USD')->format('en_US'))->toEqual('$1.00');
                expect(Money::createFromMinorUnits(100.00, 'USD')->format('en_US'))->toEqual('$1.00');
                expect(Money::createFromMinorUnits('100', 'USD')->format('en_US'))->toEqual('$1.00');
            });
        });

        describe('Arithmetic Operations', function (): void {
            test('performs addition', function (): void {
                $a = Money::createFromMajorUnits(100, 'USD');
                $b = Money::createFromMajorUnits(100, 'USD');

                expect($a->add($b)->toString())->toEqual('USD 200.00');
            });

            test('performs subtraction', function (): void {
                $a = Money::createFromMajorUnits(200, 'USD');
                $b = Money::createFromMajorUnits(100, 'USD');

                expect($a->subtract($b)->toString())->toEqual('USD 100.00');
            });

            test('performs multiplication', function (): void {
                $a = Money::createFromMajorUnits(100, 'USD');

                expect($a->multiply(2)->toString())->toEqual('USD 200.00');
            });

            test('performs division', function (): void {
                $a = Money::createFromMajorUnits(200, 'USD');

                expect($a->divide(2)->toString())->toEqual('USD 100.00');
            });

            test('gets absolute value', function (): void {
                $negative = Money::createFromMajorUnits(-100, 'USD');
                $absolute = $negative->abs();

                expect($absolute->toString())->toEqual('USD 100.00');
                expect($absolute->isPositive())->toBeTrue();

                $positive = Money::createFromMajorUnits(100, 'USD');
                expect($positive->abs()->toString())->toEqual('USD 100.00');
            });

            test('negates amounts', function (): void {
                $positive = Money::createFromMajorUnits(100, 'USD');
                $negated = $positive->negated();

                expect($negated->toString())->toEqual('USD -100.00');
                expect($negated->isNegative())->toBeTrue();

                $negative = Money::createFromMajorUnits(-50, 'EUR');
                $negatedNegative = $negative->negated();

                expect($negatedNegative->toString())->toEqual('EUR 50.00');
                expect($negatedNegative->isPositive())->toBeTrue();
            });
        });

        describe('Comparison Operations', function (): void {
            test('checks if value is zero', function (): void {
                $zero = Money::createFromMajorUnits(0, 'USD');
                $nonZero = Money::createFromMajorUnits(100, 'USD');

                expect($zero->isZero())->toBeTrue();
                expect($nonZero->isZero())->toBeFalse();
            });

            test('checks if value is positive', function (): void {
                $positive = Money::createFromMajorUnits(100, 'USD');
                $negative = Money::createFromMajorUnits(-100, 'USD');

                expect($positive->isPositive())->toBeTrue();
                expect($negative->isPositive())->toBeFalse();
            });

            test('checks if value is negative', function (): void {
                $negative = Money::createFromMajorUnits(-100, 'USD');
                $positive = Money::createFromMajorUnits(100, 'USD');

                expect($negative->isNegative())->toBeTrue();
                expect($positive->isNegative())->toBeFalse();
            });

            test('checks positive or zero', function (): void {
                $positive = Money::createFromMajorUnits(100, 'USD');
                $zero = Money::createZero('USD');
                $negative = Money::createFromMajorUnits(-100, 'USD');

                expect($positive->isPositiveOrZero())->toBeTrue();
                expect($zero->isPositiveOrZero())->toBeTrue();
                expect($negative->isPositiveOrZero())->toBeFalse();
            });

            test('checks negative or zero', function (): void {
                $negative = Money::createFromMajorUnits(-100, 'USD');
                $zero = Money::createZero('USD');
                $positive = Money::createFromMajorUnits(100, 'USD');

                expect($negative->isNegativeOrZero())->toBeTrue();
                expect($zero->isNegativeOrZero())->toBeTrue();
                expect($positive->isNegativeOrZero())->toBeFalse();
            });

            test('checks if value is less than other', function (): void {
                $value = Money::createFromMajorUnits(50, 'USD');
                $other = Money::createFromMajorUnits(100, 'USD');

                expect($value->isLessThan($other))->toBeTrue();
                expect($other->isLessThan($value))->toBeFalse();
            });

            test('checks if value is less than or equal to other', function (): void {
                $value = Money::createFromMajorUnits(50, 'USD');
                $other = Money::createFromMajorUnits(50, 'USD');
                $greater = Money::createFromMajorUnits(100, 'USD');

                expect($value->isLessThanOrEqualTo($other))->toBeTrue();
                expect($greater->isLessThanOrEqualTo($value))->toBeFalse();
            });

            test('checks if value is greater than other', function (): void {
                $value = Money::createFromMajorUnits(100, 'USD');
                $other = Money::createFromMajorUnits(50, 'USD');

                expect($value->isGreaterThan($other))->toBeTrue();
                expect($other->isGreaterThan($value))->toBeFalse();
            });

            test('checks if value is greater than or equal to other', function (): void {
                $value = Money::createFromMajorUnits(100, 'USD');
                $other = Money::createFromMajorUnits(100, 'USD');
                $lesser = Money::createFromMajorUnits(50, 'USD');

                expect($value->isGreaterThanOrEqualTo($other))->toBeTrue();
                expect($lesser->isGreaterThanOrEqualTo($value))->toBeFalse();
            });

            test('checks equality between money objects', function (): void {
                $money1 = Money::createFromMajorUnits(100, 'USD');
                $money2 = Money::createFromMajorUnits(100, 'USD');
                $money3 = Money::createFromMajorUnits(50, 'USD');

                expect($money1->isEqualTo($money2))->toBeTrue();
                expect($money1->isEqualTo($money3))->toBeFalse();
            });
        });

        describe('Accessors', function (): void {
            test('gets minor units for database storage', function (): void {
                $money = Money::createFromMajorUnits(100.50, 'USD');
                expect($money->getAmountInMinorUnits())->toEqual(10_050);

                $zero = Money::createZero('USD');
                expect($zero->getAmountInMinorUnits())->toEqual(0);
            });

            test('gets major units as float', function (): void {
                $money = Money::createFromMajorUnits(100.50, 'USD');
                expect($money->getAmountInMajorUnits())->toEqual(100.50);

                $zero = Money::createZero('USD');
                expect($zero->getAmountInMajorUnits())->toEqual(0.0);
            });

            test('gets currency as string', function (): void {
                $money = Money::createFromMajorUnits(100, 'USD');
                expect($money->getCurrency())->toEqual('USD');

                $eurMoney = Money::createFromMajorUnits(100, 'EUR');
                expect($eurMoney->getCurrency())->toEqual('EUR');
            });

            test('serializes to array', function (): void {
                $money = Money::createFromMajorUnits(100.50, 'USD');
                $array = $money->toArray();

                expect($array)->toHaveKeys(['amount_in_minor_units', 'amount_in_major_units', 'currency']);
                expect($array['currency'])->toEqual('USD');
                expect($array['amount_in_major_units'])->toEqual(100.50);
                expect($array['amount_in_minor_units'])->toEqual(10_050);
            });
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid currency code', function (): void {
            Money::createFromMajorUnits(100, 'XXX');
        })->throws(InvalidCurrencyCodeException::class);
    });

    describe('Edge Cases', function (): void {
        test('maintains precision through calculations', function (): void {
            $money = Money::createFromMajorUnits(100.01, 'USD');
            $result = $money->multiply(3)->divide(3);

            expect($result->getAmountInMajorUnits())->toEqual(100.01);
        });

        test('negating zero returns zero', function (): void {
            $zero = Money::createZero('USD');
            $negatedZero = $zero->negated();

            expect($negatedZero->isZero())->toBeTrue();
            expect($negatedZero->toString())->toEqual('USD 0.00');
        });

        test('handles currencies with zero decimal places', function (): void {
            $jpy = Money::createFromMajorUnits(1_000, 'JPY');
            expect($jpy->toString())->toEqual('JPY 1000');
        });

        test('handles currencies with three decimal places', function (): void {
            $bhd = Money::createFromMajorUnits(10.250, 'BHD');
            expect($bhd->toString())->toEqual('BHD 10.250');
        });

        test('handles very large amounts', function (): void {
            $large = Money::createFromMajorUnits(9_999_999_999.99, 'USD');
            expect($large->getAmountInMajorUnits())->toEqual(9_999_999_999.99);
        });

        test('handles very small amounts', function (): void {
            $small = Money::createFromMajorUnits(0.01, 'USD');
            expect($small->getAmountInMajorUnits())->toEqual(0.01);
            expect($small->getAmountInMinorUnits())->toEqual(1);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
