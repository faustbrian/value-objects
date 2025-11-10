<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Data\Core\AbstractData;
use Cline\ValueObjects\AbstractValueObject;
use Tests\Fixtures\NestedTestValueObject;
use Tests\Fixtures\TestValueObject;

describe('AbstractValueObject', function (): void {
    describe('Happy Paths', function (): void {
        test('equality comparison returns true for identical values', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42, 'optional');
            $vo2 = new TestValueObject('test', 42, 'optional');

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('concrete implementation enforces immutability through readonly properties', function (): void {
            // Arrange
            $vo = new TestValueObject('test', 42);

            // Act & Assert
            expect($vo->name)->toBe('test');
            expect($vo->value)->toBe(42);
            expect($vo->optional)->toBeNull();

            // Properties are readonly and cannot be modified
            expect(fn (): string => $vo->name = 'changed')->toThrow(Error::class);
        });

        test('inheritance chain works correctly', function (): void {
            // Arrange
            $vo = new TestValueObject('test', 42);

            // Act & Assert
            expect($vo)->toBeInstanceOf(TestValueObject::class);
            expect($vo)->toBeInstanceOf(AbstractValueObject::class);
            expect($vo)->toBeInstanceOf(AbstractData::class);
        });

        test('self-comparison returns true', function (): void {
            // Arrange
            $vo = new TestValueObject('test', 42);

            // Act
            $result = $vo->equals($vo);

            // Assert
            expect($result)->toBeTrue();
        });

        test('handles optional nullable properties correctly', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42);
            $vo2 = new TestValueObject('test', 42);

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        test('equality comparison returns false for different string values', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test1', 42);
            $vo2 = new TestValueObject('test2', 42);

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('equality comparison returns false for different integer values', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42);
            $vo2 = new TestValueObject('test', 43);

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('equality comparison returns false for different optional values', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42, 'optional1');
            $vo2 = new TestValueObject('test', 42, 'optional2');

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('type safety prevents comparison with different concrete types', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42);
            $nested = new TestValueObject('inner', 100);
            $vo2 = new NestedTestValueObject('id', $nested);

            // Act
            // This should return false due to type checking in equals method
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('null property handling in equals comparison', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42);
            $vo2 = new TestValueObject('test', 42);

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('null versus non-null optional property returns false', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42);
            $vo2 = new TestValueObject('test', 42, 'value');

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('nested value object comparison works correctly when equal', function (): void {
            // Arrange
            $inner1 = new TestValueObject('inner', 100, 'data');
            $inner2 = new TestValueObject('inner', 100, 'data');
            $vo1 = new NestedTestValueObject('id1', $inner1);
            $vo2 = new NestedTestValueObject('id1', $inner2);

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeTrue();
        });

        test('nested value object comparison returns false when nested differs', function (): void {
            // Arrange
            $inner1 = new TestValueObject('inner', 100, 'data1');
            $inner2 = new TestValueObject('inner', 100, 'data2');
            $vo1 = new NestedTestValueObject('id1', $inner1);
            $vo2 = new NestedTestValueObject('id1', $inner2);

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('nested value object comparison returns false when outer differs', function (): void {
            // Arrange
            $inner = new TestValueObject('inner', 100, 'data');
            $vo1 = new NestedTestValueObject('id1', $inner);
            $vo2 = new NestedTestValueObject('id2', $inner);

            // Act
            $result = $vo1->equals($vo2);

            // Assert
            expect($result)->toBeFalse();
        });

        test('mixed type properties are compared correctly', function (): void {
            // Arrange
            $vo1 = new TestValueObject('test', 42, '0');
            $vo2 = new TestValueObject('test', 42, '0');
            $vo3 = new TestValueObject('test', 42, '');

            // Act
            $result1 = $vo1->equals($vo2);
            $result2 = $vo1->equals($vo3);

            // Assert
            expect($result1)->toBeTrue();
            expect($result2)->toBeFalse();
        });

        test('value objects with zero values are handled correctly', function (): void {
            // Arrange
            $vo1 = new TestValueObject('', 0, '');
            $vo2 = new TestValueObject('', 0, '');
            $vo3 = new TestValueObject('', 0);

            // Act
            $result1 = $vo1->equals($vo2);
            $result2 = $vo1->equals($vo3);

            // Assert
            expect($result1)->toBeTrue();
            expect($result2)->toBeFalse();
        });
    });

    describe('Regressions', function (): void {
        // Add regression tests here when bugs are discovered
        // Example format:
        // test('handles edge case from bug #123', function (): void {
        //     // Test implementation
        // });
    });
});
