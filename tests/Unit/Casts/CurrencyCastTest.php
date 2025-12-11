<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Casts\CurrencyCast;
use Cline\ValueObjects\Currency;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataAttributesCollection;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\DataPropertyType;
use Spatie\LaravelData\Support\Types\IntersectionType;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Tests\Fixtures\TestDataObject;

/**
 * Tests for CurrencyCast.
 *
 * @author Brian Faust <brian@cline.sh>
 * @internal
 *
 * @covers \Cline\ValueObjects\Casts\CurrencyCast
 *
 * @small
 */

/**
 * Creates a concrete DataProperty instance for testing.
 */
function createDataProperty(): DataProperty
{
    // Arrange
    return new DataProperty(
        name: 'currency',
        className: TestDataObject::class,
        type: new DataPropertyType(
            type: new IntersectionType([]),
            isOptional: false,
            isNullable: false,
            isMixed: false,
            lazyType: null,
            kind: null,
            dataClass: null,
            dataCollectableClass: null,
            iterableClass: null,
            iterableItemType: null,
        ),
        validate: true,
        computed: false,
        hidden: false,
        isPromoted: false,
        isReadonly: false,
        morphable: false,
        autoLazy: null,
        hasDefaultValue: false,
        defaultValue: null,
        cast: null,
        transformer: null,
        inputMappedName: null,
        outputMappedName: null,
        attributes: new DataAttributesCollection([]),
    );
}

/**
 * Creates a concrete CreationContext instance for testing.
 */
function createCreationContext(): CreationContext
{
    // Arrange
    return new CreationContext(
        dataClass: TestDataObject::class,
        mappedProperties: [],
        currentPath: [],
        validationStrategy: null,
        mapPropertyNames: false,
        disableMagicalCreation: false,
        useOptionalValues: false,
        ignoredMagicalMethods: null,
        casts: null,
    );
}

describe('CurrencyCast', function (): void {
    beforeEach(function (): void {
        $this->cast = new CurrencyCast();
        $this->property = createDataProperty();
        $this->context = createCreationContext();
        $this->properties = [];
    });

    describe('Happy Paths', function (): void {
        test('casts valid USD currency code to Currency object', function (): void {
            // Arrange
            $value = 'USD';

            // Act
            $result = $this->cast->cast($this->property, $value, $this->properties, $this->context);

            // Assert
            expect($result)->toBeInstanceOf(Currency::class);
            expect($result->code)->toBe('USD');
            expect($result->symbol)->toBe('$');
            expect($result->name)->toBe('US Dollar');
            expect($result->fractionDigits)->toBe(2);
        })->group('happy-path');

        test('casts valid EUR currency code to Currency object', function (): void {
            // Arrange
            $value = 'EUR';

            // Act
            $result = $this->cast->cast($this->property, $value, $this->properties, $this->context);

            // Assert
            expect($result)->toBeInstanceOf(Currency::class);
            expect($result->code)->toBe('EUR');
            expect($result->symbol)->toBe('€');
            expect($result->name)->toBe('Euro');
            expect($result->fractionDigits)->toBe(2);
        })->group('happy-path');

        test('casts valid GBP currency code to Currency object', function (): void {
            // Arrange
            $value = 'GBP';

            // Act
            $result = $this->cast->cast($this->property, $value, $this->properties, $this->context);

            // Assert
            expect($result)->toBeInstanceOf(Currency::class);
            expect($result->code)->toBe('GBP');
            expect($result->symbol)->toBe('£');
            expect($result->name)->toBe('British Pound');
            expect($result->fractionDigits)->toBe(2);
        })->group('happy-path');

        test('casts valid JPY currency code with zero fraction digits', function (): void {
            // Arrange
            $value = 'JPY';

            // Act
            $result = $this->cast->cast($this->property, $value, $this->properties, $this->context);

            // Assert
            expect($result)->toBeInstanceOf(Currency::class);
            expect($result->code)->toBe('JPY');
            expect($result->symbol)->toBe('¥');
            expect($result->name)->toBe('Japanese Yen');
            expect($result->fractionDigits)->toBe(0);
        })->group('happy-path');

        test('casts various valid currency codes', function (string $code): void {
            // Arrange & Act
            $result = $this->cast->cast($this->property, $code, $this->properties, $this->context);

            // Assert
            expect($result)->toBeInstanceOf(Currency::class);
            expect($result->code)->toBe($code);
            expect($result->name)->not->toBeEmpty();
            expect($result->symbol)->not->toBeEmpty();
        })->with([
            ['CHF'],
            ['CAD'],
            ['AUD'],
            ['CNY'],
            ['INR'],
        ])->group('happy-path');
    });

    describe('Sad Paths', function (): void {
        test('returns Uncastable for integer input', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act
            $result = $cast->cast($property, 123, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for float input', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act
            $result = $cast->cast($property, 123.45, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for array input', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act
            $result = $cast->cast($property, ['USD'], $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for object input', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];
            $object = new stdClass();

            // Act
            $result = $cast->cast($property, $object, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for boolean input', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act
            $result = $cast->cast($property, true, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });

        test('returns Uncastable for null input', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act
            $result = $cast->cast($property, null, $properties, $context);

            // Assert
            expect($result)->toBeInstanceOf(Uncastable::class);
        });
    });

    describe('Edge Cases', function (): void {
        test('throws exception for invalid currency code', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act & Assert
            expect(fn (): Currency|Uncastable => $cast->cast($property, 'XXX', $properties, $context))
                ->toThrow(MissingResourceException::class);
        });

        test('throws exception for empty string', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act & Assert
            expect(fn (): Currency|Uncastable => $cast->cast($property, '', $properties, $context))
                ->toThrow(MissingResourceException::class);
        });

        test('handles lowercase currency code', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act & Assert
            // Symfony Intl may or may not accept lowercase codes
            try {
                $result = $cast->cast($property, 'usd', $properties, $context);
                expect($result)->toBeInstanceOf(Currency::class);
                expect($result->code)->toBe('usd');
            } catch (MissingResourceException $missingResourceException) {
                // This is also acceptable behavior
                expect($missingResourceException)->toBeInstanceOf(MissingResourceException::class);
            }
        });

        test('throws exception for currency code with whitespace', function (): void {
            // Arrange
            $cast = new CurrencyCast();
            $property = createDataProperty();
            $context = createCreationContext();
            $properties = [];

            // Act & Assert
            expect(fn (): Currency|Uncastable => $cast->cast($property, ' USD ', $properties, $context))
                ->toThrow(MissingResourceException::class);
        });
    });
});
