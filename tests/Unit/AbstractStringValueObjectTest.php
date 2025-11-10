<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tests\Fixtures\AlternativeStringValueObject;
use Tests\Fixtures\TestStringValueObject;

dataset('valid_strings', [
    'simple string' => ['test'],
    'string with spaces' => ['hello world'],
    'string with numbers' => ['test123'],
    'string with special chars' => ['test@#$%'],
    'unicode string' => ['Hello 世界 🌍'],
    'string with leading whitespace' => ['  leading'],
    'string with trailing whitespace' => ['trailing  '],
    'string with both whitespace' => ['  both  '],
]);

dataset('invalid_strings', [
    'empty string' => [''],
    'only spaces' => ['   '],
    'only tabs' => ["\t\t"],
    'only newlines' => ["\n\n"],
    'mixed whitespace' => ["  \t\n  "],
]);

describe('AbstractStringValueObject', function (): void {
    describe('Happy Paths', function (): void {
        test('creates instance with valid string', function (string $value): void {
            // Arrange
            $trimmedValue = mb_trim($value);

            // Act
            $vo = new TestStringValueObject($value);

            // Assert
            expect($vo)->toBeInstanceOf(TestStringValueObject::class);
            expect($vo->value())->toBe($trimmedValue);
        })->with('valid_strings');

        test('trims whitespace from input', function (): void {
            // Arrange
            $input = '  trimmed  ';
            $expected = 'trimmed';

            // Act
            $vo = new TestStringValueObject($input);

            // Assert
            expect($vo->value())->toBe($expected);
        });

        test('handles unicode strings correctly', function (): void {
            // Arrange
            $input = '  Hello 世界 🌍  ';
            $expected = 'Hello 世界 🌍';

            // Act
            $vo = new TestStringValueObject($input);

            // Assert
            expect($vo->value())->toBe($expected);
        });

        test('returns value via __toString method', function (): void {
            // Arrange
            $value = 'test string';
            $vo = new TestStringValueObject($value);

            // Act
            $result = (string) $vo;

            // Assert
            expect($result)->toBe($value);
        });

        test('returns value via value method', function (): void {
            // Arrange
            $value = 'test string';
            $vo = new TestStringValueObject($value);

            // Act
            $result = $vo->value();

            // Assert
            expect($result)->toBe($value);
        });

        test('equals returns true for same class and value', function (): void {
            // Arrange
            $vo1 = new TestStringValueObject('test');
            $vo2 = new TestStringValueObject('test');

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('equals returns false for same class different value', function (): void {
            // Arrange
            $vo1 = new TestStringValueObject('test1');
            $vo2 = new TestStringValueObject('test2');

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid strings', function (string $value): void {
            // Arrange & Act & Assert
            new TestStringValueObject($value);
        })->with('invalid_strings')->throws(InvalidArgumentException::class);

        test('throws exception with correct message for empty string', function (): void {
            // Arrange
            $expectedMessage = 'Tests\Fixtures\TestStringValueObject cannot be empty';

            // Act & Assert
            expect(fn (): TestStringValueObject => new TestStringValueObject(''))
                ->toThrow(InvalidArgumentException::class, $expectedMessage);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles very long strings', function (): void {
            // Arrange
            $longString = str_repeat('a', 10_000);

            // Act
            $vo = new TestStringValueObject($longString);

            // Assert
            expect($vo->value())->toBe($longString);
            expect(mb_strlen($vo->value()))->toBe(10_000);
        });

        test('preserves special characters', function (): void {
            // Arrange
            $special = '!@#$%^&*()_+-=[]{}|;\':",./<>?`~';

            // Act
            $vo = new TestStringValueObject($special);

            // Assert
            expect($vo->value())->toBe($special);
        });

        test('equals returns false for different class same value', function (): void {
            // Arrange
            $vo1 = new TestStringValueObject('test');
            $vo2 = new AlternativeStringValueObject('test');

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('handles strings with only non-breaking spaces after trim', function (): void {
            // Arrange
            $input = '  test  ';

            // Act
            $vo = new TestStringValueObject($input);

            // Assert
            expect($vo->value())->toBe('test');
        });

        test('compares instances created with whitespace correctly', function (): void {
            // Arrange
            $vo1 = new TestStringValueObject('  test  ');
            $vo2 = new TestStringValueObject('test');

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeTrue();
        });
    });

    describe('Regressions', function (): void {
        // Add regression tests when bugs are discovered
    });
});
