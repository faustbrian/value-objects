<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Casts\LanguageCast;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidLanguageCodeException;
use Cline\ValueObjects\Language;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

// Dataset for valid language codes with their expected localized names
dataset('valid_language_strings', [
    'English' => ['en', 'English'],
    'French' => ['fr', 'French'],
    'Spanish' => ['es', 'Spanish'],
    'German' => ['de', 'German'],
    'Italian' => ['it', 'Italian'],
    'Portuguese' => ['pt', 'Portuguese'],
    'Russian' => ['ru', 'Russian'],
    'Japanese' => ['ja', 'Japanese'],
    'Chinese' => ['zh', 'Chinese'],
    'Korean' => ['ko', 'Korean'],
    'Arabic' => ['ar', 'Arabic'],
    'Hindi' => ['hi', 'Hindi'],
    'Dutch' => ['nl', 'Dutch'],
    'Swedish' => ['sv', 'Swedish'],
    'Polish' => ['pl', 'Polish'],
]);

// Dataset for invalid input types that should return Uncastable
dataset('invalid_types', [
    'null' => [null],
    'integer' => [123],
    'float' => [123.45],
    'boolean true' => [true],
    'boolean false' => [false],
    'array' => [['en']],
    'object' => [(object) ['language' => 'en']],
    'resource' => [fopen('php://memory', 'rb')],
]);

// Dataset for invalid language code strings
dataset('invalid_language_strings', [
    'invalid code' => ['xx'],
    'empty string' => [''],
    'uppercase code' => ['EN'],
    'three-letter code' => ['eng'],
    'numeric code' => ['123'],
    'special characters' => ['e!'],
    'too long' => ['engl'],
    'single character' => ['e'],
    'mixed case' => ['En'],
]);

describe('LanguageCast', function (): void {
    // Helper to create mocks for DataProperty and CreationContext
    beforeEach(function (): void {
        $this->cast = new LanguageCast();
        $this->property = Mockery::mock(DataProperty::class);
        $this->context = Mockery::mock(CreationContext::class);
        $this->properties = [];
    });

    describe('Happy Paths', function (): void {
        test('casts valid language code string to Language value object', function (
            string $code,
            string $expectedName,
        ): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, $code, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Language::class);
            expect($result->value)->toBe($code);
            expect($result->localized)->toBe($expectedName);
            expect($result->toString())->toBe($code);
            expect((string) $result)->toBe($code);
        })->with('valid_language_strings');

        test('casts different valid language codes correctly', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert for multiple languages
            $english = $cast->cast($property, 'en', $properties, $context);
            expect($english)->toBeInstanceOf(Language::class);
            expect($english->value)->toBe('en');
            expect($english->localized)->toBe('English');

            $french = $cast->cast($property, 'fr', $properties, $context);
            expect($french)->toBeInstanceOf(Language::class);
            expect($french->value)->toBe('fr');
            expect($french->localized)->toBe('French');

            $japanese = $cast->cast($property, 'ja', $properties, $context);
            expect($japanese)->toBeInstanceOf(Language::class);
            expect($japanese->value)->toBe('ja');
            expect($japanese->localized)->toBe('Japanese');
        });

        test('returns correct Language instance with all properties', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'en', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Language::class);
            expect($result->value)->toBe('en');
            expect($result->localized)->toBe('English');
            expect($result->toString())->toBe('en');
            expect((string) $result)->toBe('en');
        });

        test('maintains immutability of created Language object', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result1 = $cast->cast($property, 'en', $properties, $context);
            $result2 = $cast->cast($property, 'en', $properties, $context);

            // Assert - Different instances
            expect($result1)->not->toBe($result2);
            // But with same values
            expect($result1->value)->toBe($result2->value);
            expect($result1->isEqualTo($result2))->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        test('returns Uncastable for non-string values', function (mixed $invalidValue): void {
            // Arrange
            $cast = new LanguageCast();
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
            $cast = new LanguageCast();
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
            $cast = new LanguageCast();
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
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, ['en'], $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('throws exception for invalid language code strings', function (string $invalidCode): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Language|Uncastable => $cast->cast($property, $invalidCode, $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);
        })->with('invalid_language_strings');

        test('throws exception for empty string', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Language|Uncastable => $cast->cast($property, '', $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('handles less common language codes', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert for Icelandic
            $icelandic = $cast->cast($property, 'is', $properties, $context);
            expect($icelandic)->toBeInstanceOf(Language::class);
            expect($icelandic->value)->toBe('is');
            expect($icelandic->localized)->toBe('Icelandic');

            // Act & Assert for Basque
            $basque = $cast->cast($property, 'eu', $properties, $context);
            expect($basque)->toBeInstanceOf(Language::class);
            expect($basque->value)->toBe('eu');
            expect($basque->localized)->toBe('Basque');

            // Act & Assert for Welsh
            $welsh = $cast->cast($property, 'cy', $properties, $context);
            expect($welsh)->toBeInstanceOf(Language::class);
            expect($welsh->value)->toBe('cy');
            expect($welsh->localized)->toBe('Welsh');
        });

        test('rejects uppercase language codes', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Language|Uncastable => $cast->cast($property, 'EN', $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);
        });

        test('rejects three-letter ISO 639-2 codes', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Language|Uncastable => $cast->cast($property, 'eng', $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);
        });

        test('rejects numeric language codes', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert
            expect(fn (): Language|Uncastable => $cast->cast($property, '123', $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);
        });

        test('handles languages with special characters in names', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act - Norwegian Bokmål
            $norwegian = $cast->cast($property, 'nb', $properties, $context);

            // Assert
            expect($norwegian)->toBeInstanceOf(Language::class);
            expect($norwegian->value)->toBe('nb');
            expect($norwegian->localized)->toContain('Norwegian');
        });

        test('returns Uncastable for object values', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];
            $object = (object) ['language' => 'en'];

            // Act
            $result = $cast->cast($property, $object, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for boolean values', function (): void {
            // Arrange
            $cast = new LanguageCast();
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

        test('handles whitespace in language codes', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act & Assert - Leading/trailing spaces should fail
            expect(fn (): Language|Uncastable => $cast->cast($property, ' en', $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);

            expect(fn (): Language|Uncastable => $cast->cast($property, 'en ', $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);

            expect(fn (): Language|Uncastable => $cast->cast($property, ' en ', $properties, $context))
                ->toThrow(InvalidLanguageCodeException::class);
        });

        test('maintains type safety with strict comparison', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result = $cast->cast($property, 'en', $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Language::class);
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
            $cast = new LanguageCast();
            $property1 = Mockery::mock(DataProperty::class);
            $property2 = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result1 = $cast->cast($property1, 'en', $properties, $context);
            $result2 = $cast->cast($property2, 'fr', $properties, $context);

            // Assert
            expect($result1)->toBeInstanceOf(Language::class);
            expect($result1->value)->toBe('en');
            expect($result2)->toBeInstanceOf(Language::class);
            expect($result2->value)->toBe('fr');
        });

        test('works with different CreationContext instances', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context1 = Mockery::mock(CreationContext::class);
            $context2 = Mockery::mock(CreationContext::class);
            $properties = [];

            // Act
            $result1 = $cast->cast($property, 'en', $properties, $context1);
            $result2 = $cast->cast($property, 'fr', $properties, $context2);

            // Assert
            expect($result1)->toBeInstanceOf(Language::class);
            expect($result1->value)->toBe('en');
            expect($result2)->toBeInstanceOf(Language::class);
            expect($result2->value)->toBe('fr');
        });

        test('ignores properties array content', function (): void {
            // Arrange
            $cast = new LanguageCast();
            $property = Mockery::mock(DataProperty::class);
            $context = Mockery::mock(CreationContext::class);
            $properties1 = ['other' => 'value'];
            $properties2 = ['language' => 'fr', 'foo' => 'bar'];

            // Act
            $result1 = $cast->cast($property, 'en', $properties1, $context);
            $result2 = $cast->cast($property, 'en', $properties2, $context);

            // Assert - Properties array doesn't affect the result
            expect($result1)->toBeInstanceOf(Language::class);
            expect($result1->value)->toBe('en');
            expect($result2)->toBeInstanceOf(Language::class);
            expect($result2->value)->toBe('en');
            expect($result1->isEqualTo($result2))->toBeTrue();
        });
    });
});
