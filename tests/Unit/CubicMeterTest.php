<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\CubicMeter;
use Cline\ValueObjects\Exceptions\InvalidDimensionsException;

describe('CubicMeter', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from meters', function (): void {
            $volume = CubicMeter::createFromMeter(10, 10, 10);

            expect($volume)->toBeInstanceOf(CubicMeter::class);
            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from meters as array', function (): void {
            $volume = CubicMeter::createFromMeterArray([
                'length' => 10,
                'width' => 10,
                'height' => 10,
            ]);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from decimeters', function (): void {
            $volume = CubicMeter::createFromDecimeter(100, 100, 100);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from decimeters as array', function (): void {
            $volume = CubicMeter::createFromDecimeterArray([
                'length' => 100,
                'width' => 100,
                'height' => 100,
            ]);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from centimeters', function (): void {
            $volume = CubicMeter::createFromCentimeter(1_000, 1_000, 1_000);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from centimeters as array', function (): void {
            $volume = CubicMeter::createFromCentimeterArray([
                'length' => 1_000,
                'width' => 1_000,
                'height' => 1_000,
            ]);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for zero length', function (): void {
            CubicMeter::createFromMeter(0, 10, 10);
        })->throws(InvalidDimensionsException::class);

        test('throws exception for zero width', function (): void {
            CubicMeter::createFromMeter(10, 0, 10);
        })->throws(InvalidDimensionsException::class);

        test('throws exception for zero height', function (): void {
            CubicMeter::createFromMeter(10, 10, 0);
        })->throws(InvalidDimensionsException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles very small dimensions', function (): void {
            $volume = CubicMeter::createFromMeter(1, 1, 1);

            expect($volume->getVolume())->toBe(1.0);
            expect($volume->getLength())->toBe(1.0);
            expect($volume->getWidth())->toBe(1.0);
            expect($volume->getHeight())->toBe(1.0);
        });

        test('handles very large dimensions', function (): void {
            $volume = CubicMeter::createFromMeter(1_000, 1_000, 1_000);

            expect($volume->getVolume())->toBe(1_000_000_000.0);
            expect($volume->getLength())->toBe(1_000.0);
            expect($volume->getWidth())->toBe(1_000.0);
            expect($volume->getHeight())->toBe(1_000.0);
        });

        test('handles non-cubic dimensions', function (): void {
            $volume = CubicMeter::createFromCentimeter(500, 1_000, 2_000);

            expect($volume->getLength())->toBe(5.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(20.0);
            expect($volume->getVolume())->toBe(1_000.0);
        });

        test('handles fractional dimensions in decimeters', function (): void {
            $volume = CubicMeter::createFromDecimeter(55, 55, 55);

            expect($volume->getLength())->toBe(5.5);
            expect($volume->getWidth())->toBe(5.5);
            expect($volume->getHeight())->toBe(5.5);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
