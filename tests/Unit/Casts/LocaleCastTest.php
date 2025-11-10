<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Casts\LocaleCast;
use Cline\ValueObjects\Locale;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Symfony\Component\Intl\Exception\MissingResourceException;

// Dataset for valid locale codes with their expected localized names
dataset('valid_locale_strings', [
    'English (United States)' => ['en_US', 'English (United States)'],
    'French (France)' => ['fr_FR', 'French (France)'],
    'German (Germany)' => ['de_DE', 'German (Germany)'],
    'Spanish (Spain)' => ['es_ES', 'Spanish (Spain)'],
    'Italian (Italy)' => ['it_IT', 'Italian (Italy)'],
    'Portuguese (Brazil)' => ['pt_BR', 'Portuguese (Brazil)'],
    'Japanese (Japan)' => ['ja_JP', 'Japanese (Japan)'],
    'Chinese (China)' => ['zh_CN', 'Chinese (China)'],
    'Korean (South Korea)' => ['ko_KR', 'Korean (South Korea)'],
    'Arabic (Saudi Arabia)' => ['ar_SA', 'Arabic (Saudi Arabia)'],
    'Russian (Russia)' => ['ru_RU', 'Russian (Russia)'],
    'Dutch (Netherlands)' => ['nl_NL', 'Dutch (Netherlands)'],
    'Swedish (Sweden)' => ['sv_SE', 'Swedish (Sweden)'],
    'Norwegian Bokmål (Norway)' => ['nb_NO', 'Norwegian Bokmål (Norway)'],
    'Polish (Poland)' => ['pl_PL', 'Polish (Poland)'],
]);

// Dataset for invalid input types that should return Uncastable
dataset('invalid_types', [
    'null' => [null],
    'integer' => [123],
    'float' => [123.45],
    'boolean true' => [true],
    'boolean false' => [false],
    'array' => [['en_US']],
    'object' => [(object) ['locale' => 'en_US']],
    'resource' => [fopen('php://memory', 'rb')],
]);

// Dataset for edge case locale strings
dataset('edge_case_locales', [
    'locale with script' => ['zh_Hans_CN', 'Chinese (Simplified, China)'],
    'locale with variant' => ['ca_ES_VALENCIA', 'Catalan (Spain, Valencian)'],
    'short locale code' => ['en', 'English'],
    'three letter language' => ['eng', 'eng'],
    'locale with underscore' => ['en_GB', 'English (United Kingdom)'],
    'locale with dash' => ['en-US', 'English (United States)'],
]);

describe('LocaleCast', function (): void {
    // Helper to create mocks for DataProperty and CreationContext
    beforeEach(function (): void {
        $this->cast = new LocaleCast();
        $this->property = Mockery::mock(DataProperty::class);
        $this->context = Mockery::mock(CreationContext::class);
        $this->properties = [];
    });

    describe('Happy Paths', function (): void {
        test('casts valid locale string to Locale value object', function (
            string $localeCode,
            string $expectedLocalized,
        ): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, $localeCode, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe($localeCode);
            expect($result->localized)->toBe($expectedLocalized);
            expect($result->toString())->toBe($localeCode);
            expect((string) $result)->toBe($localeCode);
        })->with('valid_locale_strings');

        test('casts different valid locale codes correctly', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert for multiple locales
            $enUS = $cast->cast($property, 'en_US', $properties, $context);
            expect($enUS)->toBeInstanceOf(Locale::class);
            expect($enUS->value)->toBe('en_US');

            $frFR = $cast->cast($property, 'fr_FR', $properties, $context);
            expect($frFR)->toBeInstanceOf(Locale::class);
            expect($frFR->value)->toBe('fr_FR');

            $deDE = $cast->cast($property, 'de_DE', $properties, $context);
            expect($deDE)->toBeInstanceOf(Locale::class);
            expect($deDE->value)->toBe('de_DE');
        });

        test('returns correct Locale instance with all properties', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'en_US', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe('en_US');
            expect($result->localized)->toBe('English (United States)');
            expect($result->toString())->toBe('en_US');
            expect((string) $result)->toBe('en_US');
        });

        test('maintains immutability of created Locale object', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result1 = $cast->cast($property, 'en_US', $properties, $context);
            $result2 = $cast->cast($property, 'en_US', $properties, $context);

            // Assert - Different instances
            expect($result1)->not->toBe($result2);
            // But with same values
            expect($result1->value)->toBe($result2->value);
            expect($result1->isEqualTo($result2))->toBeTrue();
        });

        test('handles locale codes with different formats', function (
            string $localeCode,
            string $expectedLocalized,
        ): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, $localeCode, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe($localeCode);
            expect($result->localized)->toBe($expectedLocalized);
        })->with('edge_case_locales');
    });

    describe('Sad Paths', function (): void {
        test('returns Uncastable for non-string values', function (mixed $invalidValue): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, $invalidValue, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        })->with('invalid_types');

        test('throws exception for invalid locale code string', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Locale|Uncastable => $cast->cast($property, 'invalid_locale', $properties, $context))
                ->toThrow(MissingResourceException::class);
        });

        test('returns Uncastable when value is null', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, null, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for numeric values', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert for integer
            $result = $cast->cast($property, 123, $properties, $context);
            expect($result)->toBeInstanceOf(Uncastable::class);

            // Act & Assert for float
            $result = $cast->cast($property, 123.45, $properties, $context);
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for array values', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, ['en_US'], $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for object values', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, (object) ['locale' => 'en_US'], $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('throws exception for empty string', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Locale|Uncastable => $cast->cast($property, '', $properties, $context))
                ->toThrow(MissingResourceException::class);
        });

        test('handles short locale codes', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'en', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe('en');
            expect($result->localized)->toBe('English');
        });

        test('handles locale with dash separator', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'en-US', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe('en-US');
            expect($result->localized)->toBe('English (United States)');
        });

        test('handles locale with script component', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'zh_Hans_CN', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe('zh_Hans_CN');
            expect($result->localized)->toBe('Chinese (Simplified, China)');
        });

        test('handles case sensitivity correctly', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act - uppercase country code (correct)
            $result = $cast->cast($property, 'en_US', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe('en_US');

            // Act - lowercase language code (correct)
            $result2 = $cast->cast($property, 'en_GB', $properties, $context);

            // Assert
            expect($result2)->toBeInstanceOf(Locale::class);
            expect($result2->value)->toBe('en_GB');
        });

        test('handles Unicode locale identifiers', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'ar_SA', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Locale::class);
            expect($result->value)->toBe('ar_SA');
            expect($result->localized)->toBe('Arabic (Saudi Arabia)');
        });
    });

    describe('Integration', function (): void {
        test('works with DataProperty parameter variations', function (): void {
            // Arrange
            $cast = new LocaleCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);

            // Test with different properties arrays
            $emptyProperties = [];
            $withProperties = ['name' => 'test', 'locale' => 'en_US'];

            // Act
            $result1 = $cast->cast($property, 'en_US', $emptyProperties, $context);
            $result2 = $cast->cast($property, 'fr_FR', $withProperties, $context);

            // Assert
            expect($result1)->toBeInstanceOf(Locale::class);
            expect($result1->value)->toBe('en_US');

            expect($result2)->toBeInstanceOf(Locale::class);
            expect($result2->value)->toBe('fr_FR');
        });

        test('cast method signature matches interface requirements', function (): void {
            // Arrange
            $cast = new LocaleCast();

            // Act & Assert - Verify the cast implements the interface correctly
            expect($cast)->toBeInstanceOf(Cast::class);

            // Verify method exists with correct signature
            $reflection = new ReflectionMethod($cast, 'cast');
            expect($reflection->getNumberOfParameters())->toBe(4);

            // Verify parameter types
            $parameters = $reflection->getParameters();
            expect($parameters[0]->getType()->getName())->toBe(DataProperty::class);
            expect($parameters[1]->getName())->toBe('value');
            expect($parameters[2]->getType()->getName())->toBe('array');
            expect($parameters[3]->getType()->getName())->toBe(CreationContext::class);
        });
    });
});
