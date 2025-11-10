<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brick\PhoneNumber\PhoneNumberParseException;
use Cline\ValueObjects\Casts\PhoneNumberCast;
use Cline\ValueObjects\PhoneNumber;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

// Dataset for valid phone numbers with various formats
dataset('valid_phone_numbers', [
    'US number with parentheses' => ['(212) 456-7890', 'US', '+12124567890'],
    'US number with dashes' => ['212-456-7890', 'US', '+12124567890'],
    'US number plain' => ['2124567890', 'US', '+12124567890'],
    'US number with country code' => ['+1 212 456 7890', 'US', '+12124567890'],
    'UK London number' => ['020 7946 0958', 'GB', '+442079460958'],
    'UK mobile number' => ['07911 123456', 'GB', '+447911123456'],
    'UK with country code' => ['+44 20 7946 0958', 'GB', '+442079460958'],
    'German number' => ['030 12345678', 'DE', '+493012345678'],
    'German mobile' => ['0151 12345678', 'DE', '+4915112345678'],
    'German with country code' => ['+49 30 12345678', 'DE', '+493012345678'],
    'French number' => ['01 23 45 67 89', 'FR', '+33123456789'],
    'French mobile' => ['06 12 34 56 78', 'FR', '+33612345678'],
    'Swedish number' => ['08-123 456 78', 'SE', '+46812345678'],
    'Swedish mobile' => ['070-123 45 67', 'SE', '+46701234567'],
]);

// Dataset for invalid input types
dataset('invalid_types', [
    'integer' => [12_345],
    'float' => [123.45],
    'boolean true' => [true],
    'boolean false' => [false],
    'null' => [null],
    'array' => [['555-1234']],
    'object' => [(object) ['phone' => '555-1234']],
    'empty array' => [[]],
    'resource' => [\STDIN],
]);

// Dataset for different country codes
dataset('country_codes', [
    'United States' => ['US'],
    'United Kingdom' => ['GB'],
    'Germany' => ['DE'],
    'France' => ['FR'],
    'Sweden' => ['SE'],
    'Canada' => ['CA'],
    'Australia' => ['AU'],
    'Japan' => ['JP'],
    'China' => ['CN'],
    'India' => ['IN'],
]);

// Dataset for invalid phone number strings
dataset('invalid_phone_strings', [
    'empty string' => [''],
    'letters only' => ['ABCDEFGHIJ'],
    'special chars only' => ['!@#$%^&*()'],
    'too short' => ['123'],
    'too long for any country' => ['12345678901234567890'],
    'invalid format' => ['555-CALL-NOW'],
    'partial number' => ['555-12'],
]);

describe('PhoneNumberCast', function (): void {
    // Happy Paths
    describe('Happy Paths', function (): void {
        test('casts valid string phone numbers to PhoneNumber objects', function (string $input, string $country, string $expected): void {
            // Arrange
            $cast = new PhoneNumberCast($country);
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, $input, [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->toString())->toBe($expected)
                ->and($result->isValid)->toBeTrue();
        })->with('valid_phone_numbers');

        test('uses US as default country code when none provided', function (): void {
            // Arrange
            $cast = new PhoneNumberCast();
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, '212-456-7890', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->toString())->toBe('+12124567890')
                ->and($result->regionCode)->toBe('US');
        });

        test('respects custom default country code', function (string $country): void {
            // Arrange
            $cast = new PhoneNumberCast($country);
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            // Using a simple test number that should parse in any country
            $result = $cast->cast($property, '+1 555 123 4567', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->countryCode)->toBe('1');
        })->with('country_codes');

        test('handles international format with plus sign', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('US');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, '+44 20 7946 0958', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->toString())->toBe('+442079460958')
                ->and($result->regionCode)->toBe('GB');
        });

        test('preserves phone number metadata', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('US');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, '(212) 555-1234', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->countryCode)->toBe('1')
                ->and($result->nationalNumber)->toContain('2125551234')
                ->and($result->regionCode)->toBe('US')
                ->and($result->isPossible)->toBeTrue()
                ->and($result->isValid)->toBeTrue();
        });
    });

    // Sad Paths
    describe('Sad Paths', function (): void {
        test('returns Uncastable for non-string types', function (mixed $input): void {
            // Arrange
            $cast = new PhoneNumberCast();
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, $input, [], $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        })->with('invalid_types');

        test('throws exception for invalid phone number strings', function (string $input): void {
            // Arrange
            $cast = new PhoneNumberCast();
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act & Assert
            expect(fn (): PhoneNumber|Uncastable => $cast->cast($property, $input, [], $context))
                ->toThrow(PhoneNumberParseException::class);
        })->with('invalid_phone_strings');

        test('throws exception for phone number invalid in specified country', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('US');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act & Assert
            // UK local number without country code, trying to parse as US
            expect(fn (): PhoneNumber|Uncastable => $cast->cast($property, '020 7946 0958', [], $context))
                ->toThrow(PhoneNumberParseException::class);
        });
    });

    // Edge Cases
    describe('Edge Cases', function (): void {
        test('handles phone numbers with extensions', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('US');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, '+1 555 123 4567 ext. 123', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->toString())->toBe('+15551234567');
        });

        test('handles phone numbers with various separators', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('US');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            $numbers = [
                '555.123.4567',     // dots
                '555 123 4567',     // spaces
                '555/123/4567',     // slashes
                '(555)123-4567',    // mixed
            ];

            foreach ($numbers as $number) {
                // Act
                $result = $cast->cast($property, $number, [], $context);

                // Assert
                expect($result)->toBeInstanceOf(PhoneNumber::class)
                    ->and($result->toString())->toBe('+15551234567');
            }
        });

        test('handles leading and trailing whitespace', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('US');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, '  555-123-4567  ', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->toString())->toBe('+15551234567');
        });

        test('handles phone numbers with country code but different default', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('GB'); // Default to GB
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act - US number with country code
            $result = $cast->cast($property, '+1 555 123 4567', [], $context);

            // Assert - Should still parse as US number
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->toString())->toBe('+15551234567')
                ->and($result->regionCode)->toBe('US');
        });

        test('handles mobile vs landline number types', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('GB');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act - Mobile number
            $mobile = $cast->cast($property, '07700 900123', [], $context);

            // Act - Landline number
            $landline = $cast->cast($property, '020 7946 0958', [], $context);

            // Assert
            expect($mobile)->toBeInstanceOf(PhoneNumber::class)
                ->and($mobile->numberType)->not->toBeNull()
                ->and($landline)->toBeInstanceOf(PhoneNumber::class)
                ->and($landline->numberType)->not->toBeNull();
        });

        test('handles null country code by defaulting to US', function (): void {
            // Arrange
            $cast = new PhoneNumberCast();
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $result = $cast->cast($property, '555-123-4567', [], $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->regionCode)->toBe('US');
        });
    });

    // Integration Tests
    describe('Integration', function (): void {
        test('works with DataProperty from Spatie Data', function (): void {
            // Arrange
            $cast = new PhoneNumberCast('US');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();
            $properties = ['name' => 'John Doe', 'phone' => '555-123-4567'];

            // Act
            $result = $cast->cast($property, '555-123-4567', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(PhoneNumber::class)
                ->and($result->toString())->toBe('+15551234567');
        });

        test('multiple casts with different country defaults work independently', function (): void {
            // Arrange
            $usCast = new PhoneNumberCast('US');
            $ukCast = new PhoneNumberCast('GB');
            $property = createPhoneNumberDataProperty();
            $context = createPhoneNumberCreationContext();

            // Act
            $usResult = $usCast->cast($property, '555-123-4567', [], $context);
            $ukResult = $ukCast->cast($property, '020 7946 0958', [], $context);

            // Assert
            expect($usResult)->toBeInstanceOf(PhoneNumber::class)
                ->and($usResult->regionCode)->toBe('US')
                ->and($ukResult)->toBeInstanceOf(PhoneNumber::class)
                ->and($ukResult->regionCode)->toBe('GB');
        });
    });
});

// Helper functions to create mock objects for testing
function createPhoneNumberDataProperty(): DataProperty
{
    // Create a mock DataProperty that satisfies the interface requirements
    // Using reflection to create without full constructor params since we don't need all functionality
    $reflection = new ReflectionClass(DataProperty::class);
    $property = $reflection->newInstanceWithoutConstructor();

    // Set minimal required properties using reflection
    $nameProperty = $reflection->getProperty('name');
    $nameProperty->setValue($property, 'phone');

    return $property;
}

function createPhoneNumberCreationContext(): CreationContext
{
    // Create a mock CreationContext that satisfies the interface requirements
    $reflection = new ReflectionClass(CreationContext::class);

    return $reflection->newInstanceWithoutConstructor();
}
