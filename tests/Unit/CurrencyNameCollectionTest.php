<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\CurrencyNameCollection;

describe('CurrencyNameCollection', function (): void {
    describe('Happy Paths', function (): void {
        test('creates collection from array', function (): void {
            $currencyNames = ['USD' => 'US Dollar', 'EUR' => 'Euro'];
            $collection = CurrencyNameCollection::fromArray($currencyNames);

            expect($collection)->toBeInstanceOf(CurrencyNameCollection::class);
            expect($collection->all())->toBe($currencyNames);
        });

        test('creates collection from symfony intl', function (): void {
            $collection = CurrencyNameCollection::fromSymfonyIntl('en');

            expect($collection)->toBeInstanceOf(CurrencyNameCollection::class);
            expect($collection->getName('USD'))->toBe('US Dollar');
            expect($collection->getName('EUR'))->toBe('Euro');
            expect($collection->count())->toBeGreaterThan(100);
        });

        test('gets currency name by code', function (): void {
            $currencyNames = ['USD' => 'US Dollar', 'EUR' => 'Euro', 'SEK' => 'Swedish Krona'];
            $collection = CurrencyNameCollection::fromArray($currencyNames);

            expect($collection->getName('USD'))->toBe('US Dollar');
            expect($collection->getName('EUR'))->toBe('Euro');
            expect($collection->getName('SEK'))->toBe('Swedish Krona');
        });

        test('checks if name exists for currency code', function (): void {
            $currencyNames = ['USD' => 'US Dollar', 'EUR' => 'Euro'];
            $collection = CurrencyNameCollection::fromArray($currencyNames);

            expect($collection->hasName('USD'))->toBeTrue();
            expect($collection->hasName('EUR'))->toBeTrue();
        });

        test('inherits laravel collection methods', function (): void {
            $currencyNames = ['USD' => 'US Dollar', 'EUR' => 'Euro', 'SEK' => 'Swedish Krona'];
            $collection = CurrencyNameCollection::fromArray($currencyNames);

            expect($collection->keys()->all())->toBe(['USD', 'EUR', 'SEK']);
            expect($collection->values()->all())->toBe(['US Dollar', 'Euro', 'Swedish Krona']);
            expect($collection->only(['USD', 'EUR'])->all())->toBe(['USD' => 'US Dollar', 'EUR' => 'Euro']);
        });
    });

    describe('Sad Paths', function (): void {
        test('returns null for non-existent currency', function (): void {
            $currencyNames = ['USD' => 'US Dollar', 'EUR' => 'Euro'];
            $collection = CurrencyNameCollection::fromArray($currencyNames);

            expect($collection->getName('XXX'))->toBeNull();
            expect($collection->hasName('XXX'))->toBeFalse();
        });

        test('handles missing currencies gracefully', function (): void {
            $collection = CurrencyNameCollection::fromSymfonyIntl('en');

            expect($collection->getName('INVALID'))->toBeNull();
            expect($collection->hasName('INVALID'))->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles empty collection', function (): void {
            $collection = CurrencyNameCollection::fromArray([]);

            expect($collection->count())->toBe(0);
            expect($collection->isEmpty())->toBeTrue();
        });

        test('supports collection filtering', function (): void {
            $currencyNames = ['USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound'];
            $collection = CurrencyNameCollection::fromArray($currencyNames);

            $filtered = $collection->filter(fn ($name): bool => str_contains($name, 'Dollar'));

            expect($filtered->all())->toBe(['USD' => 'US Dollar']);
        });

        test('returns all keys and values', function (): void {
            $currencyNames = ['USD' => 'US Dollar', 'EUR' => 'Euro'];
            $collection = CurrencyNameCollection::fromArray($currencyNames);

            expect($collection->keys()->all())->toBe(['USD', 'EUR']);
            expect($collection->values()->all())->toBe(['US Dollar', 'Euro']);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
