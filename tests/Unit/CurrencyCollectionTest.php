<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\CurrencyCollection;

describe('CurrencyCollection', function (): void {
    describe('Happy Paths', function (): void {
        test('creates collection from array', function (): void {
            $currencies = ['USD', 'EUR', 'SEK'];
            $collection = CurrencyCollection::fromArray($currencies);

            expect($collection)->toBeInstanceOf(CurrencyCollection::class);
            expect($collection->all())->toBe($currencies);
        });

        test('creates collection from symfony intl', function (): void {
            $collection = CurrencyCollection::fromSymfonyIntl();

            expect($collection)->toBeInstanceOf(CurrencyCollection::class);
            expect($collection->contains('USD'))->toBeTrue();
            expect($collection->contains('EUR'))->toBeTrue();
            expect($collection->contains('SEK'))->toBeTrue();
            expect($collection->count())->toBeGreaterThan(100);
        });

        test('inherits laravel collection methods', function (): void {
            $collection = CurrencyCollection::fromArray(['USD', 'EUR', 'SEK']);

            expect($collection->first())->toBe('USD');
            expect($collection->last())->toBe('SEK');
            expect($collection->filter(static fn ($currency): bool => $currency === 'EUR')->count())->toBe(1);
            expect($collection->map(static fn ($currency) => mb_strtolower($currency))->all())->toBe(['usd', 'eur', 'sek']);
        });

        test('can be iterated', function (): void {
            $currencies = ['USD', 'EUR', 'SEK'];
            $collection = CurrencyCollection::fromArray($currencies);
            $result = [];

            foreach ($collection as $currency) {
                $result[] = $currency;
            }

            expect($result)->toBe($currencies);
        });
    });

    describe('Sad Paths', function (): void {
        // Add when validation is implemented
    });

    describe('Edge Cases', function (): void {
        test('removes duplicates from array', function (): void {
            $currencies = ['USD', 'EUR', 'USD', 'SEK', 'EUR'];
            $collection = CurrencyCollection::fromArray($currencies);

            expect($collection->all())->toBe(['USD', 'EUR', 'SEK']);
            expect($collection->count())->toBe(3);
        });

        test('handles empty collection', function (): void {
            $collection = CurrencyCollection::fromArray([]);

            expect($collection->count())->toBe(0);
            expect($collection->isEmpty())->toBeTrue();
        });

        test('supports collection chaining', function (): void {
            $collection = CurrencyCollection::fromArray(['USD', 'EUR', 'GBP', 'SEK']);

            $result = $collection
                ->filter(fn ($c): bool => $c !== 'USD')
                ->values()
                ->all();

            expect($result)->toBe(['EUR', 'GBP', 'SEK']);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
