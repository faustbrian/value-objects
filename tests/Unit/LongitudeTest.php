<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Longitude;

dataset('valid_longitudes', [
    'prime meridian' => [0.0],
    'international date line east' => [180.0],
    'international date line west' => [-180.0],
    'mid east' => [90.0],
    'mid west' => [-90.0],
    'with decimals' => [-122.419_4],
]);

dataset('invalid_longitudes', [
    'too high' => [181.0],
    'too low' => [-181.0],
    'far too high' => [360.0],
    'far too low' => [-360.0],
]);

describe('Longitude', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid longitude values', function (float $value): void {
            $longitude = Longitude::createFromNumber($value);

            expect($longitude->toString())->toBe((string) $value);
            expect((string) $longitude)->toBe((string) $value);
        })->with('valid_longitudes');

        test('handles boundary values', function (): void {
            $east = Longitude::createFromNumber(180.0);
            expect($east->toString())->toBe('180');

            $west = Longitude::createFromNumber(-180.0);
            expect($west->toString())->toBe('-180');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for out of range values', function (float $value): void {
            Longitude::createFromNumber($value);
        })->with('invalid_longitudes')->throws(OutOfRangeException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles zero longitude', function (): void {
            $prime = Longitude::createFromNumber(0.0);
            expect($prime->toString())->toBe('0');
        });

        test('preserves decimal precision', function (): void {
            $precise = Longitude::createFromNumber(-122.419_4);
            expect($precise->toString())->toBe('-122.4194');
        });

        test('string casting works', function (): void {
            $longitude = Longitude::createFromNumber(90.0);
            expect((string) $longitude)->toBe('90');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
