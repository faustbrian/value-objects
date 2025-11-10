<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\CubicCentimeter;
use Cline\ValueObjects\Exceptions\InvalidDimensionsException;

describe('CubicCentimeter', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from meters', function (): void {
            $volume = CubicCentimeter::createFromMeter(1, 1, 1);

            expect($volume)->toBeInstanceOf(CubicCentimeter::class);
            expect($volume->getVolume())->toBe(1_000_000.0);
            expect($volume->getLength())->toBe(100.0);
            expect($volume->getWidth())->toBe(100.0);
            expect($volume->getHeight())->toBe(100.0);
            expect($volume->toString())->toBe('1,000,000');
        });

        test('creates from meters as array', function (): void {
            $volume = CubicCentimeter::createFromMeterArray([
                'length' => 1,
                'width' => 1,
                'height' => 1,
            ]);

            expect($volume->getVolume())->toBe(1_000_000.0);
            expect($volume->getLength())->toBe(100.0);
            expect($volume->getWidth())->toBe(100.0);
            expect($volume->getHeight())->toBe(100.0);
            expect($volume->toString())->toBe('1,000,000');
        });

        test('creates from decimeters', function (): void {
            $volume = CubicCentimeter::createFromDecimeter(10, 10, 10);

            expect($volume->getVolume())->toBe(1_000_000.0);
            expect($volume->getLength())->toBe(100.0);
            expect($volume->getWidth())->toBe(100.0);
            expect($volume->getHeight())->toBe(100.0);
            expect($volume->toString())->toBe('1,000,000');
        });

        test('creates from decimeters as array', function (): void {
            $volume = CubicCentimeter::createFromDecimeterArray([
                'length' => 10,
                'width' => 10,
                'height' => 10,
            ]);

            expect($volume->getVolume())->toBe(1_000_000.0);
            expect($volume->getLength())->toBe(100.0);
            expect($volume->getWidth())->toBe(100.0);
            expect($volume->getHeight())->toBe(100.0);
            expect($volume->toString())->toBe('1,000,000');
        });

        test('creates from centimeters', function (): void {
            $volume = CubicCentimeter::createFromCentimeter(100, 100, 100);

            expect($volume->getVolume())->toBe(1_000_000.0);
            expect($volume->getLength())->toBe(100.0);
            expect($volume->getWidth())->toBe(100.0);
            expect($volume->getHeight())->toBe(100.0);
            expect($volume->toString())->toBe('1,000,000');
        });

        test('creates from centimeters as array', function (): void {
            $volume = CubicCentimeter::createFromCentimeterArray([
                'length' => 100,
                'width' => 100,
                'height' => 100,
            ]);

            expect($volume->getVolume())->toBe(1_000_000.0);
            expect($volume->getLength())->toBe(100.0);
            expect($volume->getWidth())->toBe(100.0);
            expect($volume->getHeight())->toBe(100.0);
            expect($volume->toString())->toBe('1,000,000');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for zero length', function (): void {
            CubicCentimeter::createFromMeter(0, 10, 10);
        })->throws(InvalidDimensionsException::class);

        test('throws exception for zero width', function (): void {
            CubicCentimeter::createFromMeter(10, 0, 10);
        })->throws(InvalidDimensionsException::class);

        test('throws exception for zero height', function (): void {
            CubicCentimeter::createFromMeter(10, 10, 0);
        })->throws(InvalidDimensionsException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles very small dimensions', function (): void {
            $volume = CubicCentimeter::createFromMeter(0.01, 0.01, 0.01);

            expect($volume->getVolume())->toBe(1.0);
            expect($volume->getLength())->toBe(1.0);
            expect($volume->getWidth())->toBe(1.0);
            expect($volume->getHeight())->toBe(1.0);
        });

        test('handles very large dimensions', function (): void {
            $volume = CubicCentimeter::createFromMeter(100, 100, 100);

            expect($volume->getVolume())->toBe(1_000_000_000_000.0);
            expect($volume->getLength())->toBe(10_000.0);
            expect($volume->getWidth())->toBe(10_000.0);
            expect($volume->getHeight())->toBe(10_000.0);
        });

        test('handles non-cubic dimensions', function (): void {
            $volume = CubicCentimeter::createFromCentimeter(50, 100, 200);

            expect($volume->getLength())->toBe(50.0);
            expect($volume->getWidth())->toBe(100.0);
            expect($volume->getHeight())->toBe(200.0);
            expect($volume->getVolume())->toBe(1_000_000.0);
        });

        test('handles fractional dimensions in decimeters', function (): void {
            $volume = CubicCentimeter::createFromDecimeter(5.5, 5.5, 5.5);

            expect($volume->getLength())->toBe(55.0);
            expect($volume->getWidth())->toBe(55.0);
            expect($volume->getHeight())->toBe(55.0);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
