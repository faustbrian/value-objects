<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Coordinate;

dataset('valid_coordinates', [
    'null island' => [0.0, 0.0, '0,0'],
    'san francisco' => [37.774_9, -122.419_4, '37.7749,-122.4194'],
    'north pole' => [90.0, 0.0, '90,0'],
    'south pole' => [-90.0, 0.0, '-90,0'],
    'sydney' => [-33.868_8, 151.209_3, '-33.8688,151.2093'],
]);

describe('Coordinate', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid coordinate pairs', function (float $lat, float $lon, string $expected): void {
            $coordinate = Coordinate::createFromNumber($lat, $lon);

            expect($coordinate->toString())->toBe($expected);
        })->with('valid_coordinates');

        test('handles boundary coordinates', function (): void {
            $northWest = Coordinate::createFromNumber(90.0, -180.0);
            expect($northWest->toString())->toBe('90,-180');

            $southEast = Coordinate::createFromNumber(-90.0, 180.0);
            expect($southEast->toString())->toBe('-90,180');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid latitude', function (): void {
            Coordinate::createFromNumber(100.0, 90.0);
        })->throws(OutOfRangeException::class);

        test('throws exception for invalid longitude', function (): void {
            Coordinate::createFromNumber(45.0, 200.0);
        })->throws(OutOfRangeException::class);

        test('throws exception for both invalid', function (): void {
            Coordinate::createFromNumber(100.0, 200.0);
        })->throws(OutOfRangeException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles null island coordinates', function (): void {
            $nullIsland = Coordinate::createFromNumber(0.0, 0.0);
            expect($nullIsland->toString())->toBe('0,0');
        });

        test('preserves decimal precision in both values', function (): void {
            $precise = Coordinate::createFromNumber(37.774_9, -122.419_4);
            expect($precise->toString())->toBe('37.7749,-122.4194');
        });

        test('formats with comma separator', function (): void {
            $coord = Coordinate::createFromNumber(45.0, 90.0);
            expect($coord->toString())->toContain(',');
            expect($coord->toString())->not->toContain(' ');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
