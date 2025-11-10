<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Attributes\Validation\PositiveMoney;
use Cline\ValueObjects\Money;
use Spatie\LaravelData\Support\Validation\ValidationPath;

/**
 * Create a test Money object with the specified amount.
 *
 * @param  int    $amountInMinorUnits Amount in minor units (cents)
 * @param  string $currency           ISO currency code
 * @return Money  The created Money object
 */
function createTestMoney(int $amountInMinorUnits, string $currency = 'USD'): Money
{
    return Money::createFromMinorUnits($amountInMinorUnits, $currency);
}

/**
 * Create a test money array representation.
 *
 * @param  int   $amountInMinorUnits Amount in minor units
 * @return array The money array representation
 */
function createTestMoneyArray(int $amountInMinorUnits): array
{
    return [
        'amount_in_minor_units' => $amountInMinorUnits,
        'currency' => 'USD',
    ];
}

describe('PositiveMoney', function (): void {
    describe('Happy Paths', function (): void {
        test('validates positive Money object successfully', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $money = createTestMoney(1_000); // $10.00
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('amount', $money, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });

        test('validates zero Money object successfully', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $money = createTestMoney(0); // $0.00
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('amount', $money, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });

        test('validates positive serialized array successfully', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $moneyArray = createTestMoneyArray(1_500); // $15.00
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('amount', $moneyArray, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });

        test('validates zero serialized array successfully', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $moneyArray = createTestMoneyArray(0); // $0.00
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('amount', $moneyArray, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });
    });

    describe('Sad Paths', function (): void {
        test('fails validation for negative Money object', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $money = createTestMoney(-500); // -$5.00
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('payment_amount', $money, $fail);

            // Assert
            expect($failed)->toBe(true);
            expect($failureMessage)->toBe('The :attribute amount must be positive.');
        });

        test('fails validation for negative serialized array', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $moneyArray = createTestMoneyArray(-1_000); // -$10.00
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('price', $moneyArray, $fail);

            // Assert
            expect($failed)->toBe(true);
            expect($failureMessage)->toBe('The :attribute amount must be positive.');
        });

        test('passes validation for non-numeric amount in array', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $moneyArray = [
                'amount_in_minor_units' => 'not-a-number',
                'currency' => 'USD',
            ];
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('amount', $moneyArray, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });
    });

    describe('Edge Cases', function (): void {
        test('passes validation for unknown value types', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $testCases = [
                null,
                'string-value',
                123,
                new stdClass(),
                ['some' => 'array'],
            ];

            foreach ($testCases as $value) {
                $failed = false;
                $failureMessage = '';
                $fail = function (string $message) use (&$failed, &$failureMessage): void {
                    $failed = true;
                    $failureMessage = $message;
                };

                // Act
                $rules = $attribute->getRules($validationPath);
                $validationClosure = $rules[0];
                $validationClosure('field', $value, $fail);

                // Assert
                expect($failed)->toBe(false);
                expect($failureMessage)->toBe('');
            }
        });

        test('passes validation for array without amount_in_minor_units key', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $moneyArray = [
                'amount' => 100,
                'currency' => 'USD',
            ];
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('amount', $moneyArray, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });

        test('handles very large positive amounts', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $money = createTestMoney(\PHP_INT_MAX);
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('large_amount', $money, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });

        test('handles fractional amounts that get converted to int', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $moneyArray = [
                'amount_in_minor_units' => 99.99, // Will be cast to int(99)
                'currency' => 'USD',
            ];
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('fractional', $moneyArray, $fail);

            // Assert
            expect($failed)->toBe(false);
            expect($failureMessage)->toBe('');
        });

        test('handles negative fractional amounts that get converted to int', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $moneyArray = [
                'amount_in_minor_units' => -99.99, // Will be cast to int(-99)
                'currency' => 'USD',
            ];
            $failed = false;
            $failureMessage = '';
            $fail = function (string $message) use (&$failed, &$failureMessage): void {
                $failed = true;
                $failureMessage = $message;
            };

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $validationClosure('negative_fractional', $moneyArray, $fail);

            // Assert
            expect($failed)->toBe(true);
            expect($failureMessage)->toBe('The :attribute amount must be positive.');
        });
    });

    describe('Integration with ValidationPath', function (): void {
        test('returns correct rule structure for Laravel validation', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();

            // Act
            $rules = $attribute->getRules($validationPath);

            // Assert
            expect($rules)->toBeArray();
            expect($rules)->toHaveCount(1);
            expect($rules[0])->toBeCallable();
        });

        test('validation closure has correct signature', function (): void {
            // Arrange
            $attribute = new PositiveMoney();
            $validationPath = ValidationPath::create();
            $money = createTestMoney(100);

            // Act
            $rules = $attribute->getRules($validationPath);
            $validationClosure = $rules[0];
            $reflection = new ReflectionFunction($validationClosure);
            $parameters = $reflection->getParameters();

            // Assert
            expect($parameters)->toHaveCount(3);
            expect($parameters[0]->getName())->toBe('attribute');
            expect($parameters[1]->getName())->toBe('value');
            expect($parameters[2]->getName())->toBe('fail');
        });
    });
});
