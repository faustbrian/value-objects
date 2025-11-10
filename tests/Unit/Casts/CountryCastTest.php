<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Casts\CountryCast;
use Cline\ValueObjects\Country;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidCountryCodeException;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

// Dataset for valid country codes with their expected values
dataset('valid_country_strings', [
    'United States' => ['US', 'United States', 'US', 'USA'],
    'Sweden' => ['SE', 'Sweden', 'SE', 'SWE'],
    'United Kingdom' => ['GB', 'United Kingdom', 'GB', 'GBR'],
    'Germany' => ['DE', 'Germany', 'DE', 'DEU'],
    'France' => ['FR', 'France', 'FR', 'FRA'],
    'Japan' => ['JP', 'Japan', 'JP', 'JPN'],
    'Canada' => ['CA', 'Canada', 'CA', 'CAN'],
    'Australia' => ['AU', 'Australia', 'AU', 'AUS'],
]);

// Dataset for invalid input types that should return Uncastable
dataset('invalid_types', [
    'null' => [null],
    'integer' => [123],
    'float' => [123.45],
    'boolean true' => [true],
    'boolean false' => [false],
    'array' => [['US']],
    'object' => [(object) ['country' => 'US']],
    'resource' => [fopen('php://memory', 'rb')],
]);

// Dataset for invalid country code strings
dataset('invalid_country_strings', [
    'invalid code' => ['XX'],
    'empty string' => [''],
    'lowercase code' => ['us'],
    'alpha-3 code' => ['USA'],
    'numeric code' => ['840'],
    'special characters' => ['U$'],
    'too long' => ['USAA'],
    'single character' => ['U'],
]);

describe('CountryCast', function (): void {
    // Helper to create mocks for DataProperty and CreationContext
    beforeEach(function (): void {
        $this->cast = new CountryCast();
        $this->property = Mockery::mock(DataProperty::class);
        $this->context = Mockery::mock(CreationContext::class);
        $this->properties = [];
    });

    describe('Happy Paths', function (): void {
        test('casts valid country code string to Country value object', function (
            string $code,
            string $expectedName,
            string $expectedAlpha2,
            string $expectedAlpha3,
        ): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, $code, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Country::class);
            expect($result->alpha2)->toBe($expectedAlpha2);
            expect($result->alpha3)->toBe($expectedAlpha3);
            expect($result->localized)->toBe($expectedName);
            expect($result->toString())->toBe($expectedAlpha2);
        })->with('valid_country_strings');

        test('casts different valid country codes correctly', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert for multiple countries
            $us = $cast->cast($property, 'US', $properties, $context);
            expect($us)->toBeInstanceOf(Country::class);
            expect($us->alpha2)->toBe('US');

            $gb = $cast->cast($property, 'GB', $properties, $context);
            expect($gb)->toBeInstanceOf(Country::class);
            expect($gb->alpha2)->toBe('GB');

            $jp = $cast->cast($property, 'JP', $properties, $context);
            expect($jp)->toBeInstanceOf(Country::class);
            expect($jp->alpha2)->toBe('JP');
        });

        test('returns correct Country instance with all properties', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'US', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Country::class);
            expect($result->alpha2)->toBe('US');
            expect($result->alpha3)->toBe('USA');
            expect($result->localized)->toBe('United States');
            expect($result->toString())->toBe('US');
        });

        test('maintains immutability of created Country object', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result1 = $cast->cast($property, 'US', $properties, $context);
            $result2 = $cast->cast($property, 'US', $properties, $context);

            // Assert - Different instances
            expect($result1)->not->toBe($result2);
            // But with same values
            expect($result1->alpha2)->toBe($result2->alpha2);
            expect($result1->isEqualTo($result2))->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        test('returns Uncastable for non-string values', function (mixed $invalidValue): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, $invalidValue, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        })->with('invalid_types');

        test('returns Uncastable for null value', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, null, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for integer value', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 123, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for array value', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, ['US'], $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('throws exception for invalid country code strings', function (string $invalidCode): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Country|Uncastable => $cast->cast($property, $invalidCode, $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);
        })->with('invalid_country_strings');

        test('throws exception for empty string', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Country|Uncastable => $cast->cast($property, '', $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles less common country codes', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert for Iceland
            $iceland = $cast->cast($property, 'IS', $properties, $context);
            expect($iceland)->toBeInstanceOf(Country::class);
            expect($iceland->alpha2)->toBe('IS');
            expect($iceland->alpha3)->toBe('ISL');

            // Act & Assert for Switzerland
            $switzerland = $cast->cast($property, 'CH', $properties, $context);
            expect($switzerland)->toBeInstanceOf(Country::class);
            expect($switzerland->alpha2)->toBe('CH');
            expect($switzerland->alpha3)->toBe('CHE');
        });

        test('rejects lowercase country codes', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Country|Uncastable => $cast->cast($property, 'us', $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);
        });

        test('rejects alpha-3 codes as input', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Country|Uncastable => $cast->cast($property, 'USA', $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);
        });

        test('rejects numeric country codes', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Country|Uncastable => $cast->cast($property, '840', $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);
        });

        test('handles countries with special characters in names', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act - Côte d'Ivoire
            $ivoryCoast = $cast->cast($property, 'CI', $properties, $context);

            // Assert
            expect($ivoryCoast)->toBeInstanceOf(Country::class);
            expect($ivoryCoast->alpha2)->toBe('CI');
            expect($ivoryCoast->alpha3)->toBe('CIV');
            expect($ivoryCoast->localized)->toContain('Ivoire');
        });

        test('returns Uncastable for object values', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];
            $object = (object) ['country' => 'US'];

            // Act
            $result = $cast->cast($property, $object, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for boolean values', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert for true
            $resultTrue = $cast->cast($property, true, $properties, $context);
            expect($resultTrue)->toBeInstanceOf(Uncastable::class);

            // Act & Assert for false
            $resultFalse = $cast->cast($property, false, $properties, $context);
            expect($resultFalse)->toBeInstanceOf(Uncastable::class);
        });

        test('handles whitespace in country codes', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert - Leading/trailing spaces should fail
            expect(fn (): Country|Uncastable => $cast->cast($property, ' US', $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);

            expect(fn (): Country|Uncastable => $cast->cast($property, 'US ', $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);

            expect(fn (): Country|Uncastable => $cast->cast($property, ' US ', $properties, $context))
                ->toThrow(InvalidCountryCodeException::class);
        });

        test('maintains type safety with strict comparison', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'US', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Country::class);
            expect($result)->not->toBeInstanceOf(Uncastable::class);
        });
    });

    describe('Regressions', function (): void {
        // Add regression tests when bugs are discovered
        // Example format:
        // test('handles specific bug case #123', function (): void {
        //     // Test for specific bug fix
        // });
    });

    describe('Integration', function (): void {
        test('works with different DataProperty instances', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property1 = Mockery::mock(DataProperty::class);
            $property2 = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result1 = $cast->cast($property1, 'US', $properties, $context);
            $result2 = $cast->cast($property2, 'GB', $properties, $context);

            // Assert
            expect($result1)->toBeInstanceOf(Country::class);
            expect($result1->alpha2)->toBe('US');
            expect($result2)->toBeInstanceOf(Country::class);
            expect($result2->alpha2)->toBe('GB');
        });

        test('works with different CreationContext instances', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context1 = Mockery::mock(CreationContext::class);
            $context2 = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result1 = $cast->cast($property, 'US', $properties, $context1);
            $result2 = $cast->cast($property, 'GB', $properties, $context2);

            // Assert
            expect($result1)->toBeInstanceOf(Country::class);
            expect($result1->alpha2)->toBe('US');
            expect($result2)->toBeInstanceOf(Country::class);
            expect($result2->alpha2)->toBe('GB');
        });

        test('ignores properties array content', function (): void {
            // Arrange
            $cast = new CountryCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties1 = ['other' => 'value'];
            $properties2 = ['country' => 'GB', 'foo' => 'bar'];

            // Act
            $result1 = $cast->cast($property, 'US', $properties1, $context);
            $result2 = $cast->cast($property, 'US', $properties2, $context);

            // Assert - Properties array doesn't affect the result
            expect($result1)->toBeInstanceOf(Country::class);
            expect($result1->alpha2)->toBe('US');
            expect($result2)->toBeInstanceOf(Country::class);
            expect($result2->alpha2)->toBe('US');
            expect($result1->isEqualTo($result2))->toBeTrue();
        });
    });
});
