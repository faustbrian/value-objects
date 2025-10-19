<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Latitude;

dataset('valid_latitudes', [
    'equator' => [0.0],
    'north pole' => [90.0],
    'south pole' => [-90.0],
    'mid north' => [45.0],
    'mid south' => [-45.0],
    'with decimals' => [37.774_9],
]);

dataset('invalid_latitudes', [
    'too high' => [91.0],
    'too low' => [-91.0],
    'far too high' => [180.0],
    'far too low' => [-180.0],
]);

describe('Latitude', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid latitude values', function (float $value): void {
            $latitude = Latitude::createFromNumber($value);

            expect($latitude->toString())->toBe((string) $value);
            expect((string) $latitude)->toBe((string) $value);
        })->with('valid_latitudes');

        test('handles boundary values', function (): void {
            $north = Latitude::createFromNumber(90.0);
            expect($north->toString())->toBe('90');

            $south = Latitude::createFromNumber(-90.0);
            expect($south->toString())->toBe('-90');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for out of range values', function (float $value): void {
            Latitude::createFromNumber($value);
        })->with('invalid_latitudes')->throws(OutOfRangeException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles zero latitude', function (): void {
            $equator = Latitude::createFromNumber(0.0);
            expect($equator->toString())->toBe('0');
        });

        test('preserves decimal precision', function (): void {
            $precise = Latitude::createFromNumber(37.774_9);
            expect($precise->toString())->toBe('37.7749');
        });

        test('string casting works', function (): void {
            $latitude = Latitude::createFromNumber(45.0);
            expect((string) $latitude)->toBe('45');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
