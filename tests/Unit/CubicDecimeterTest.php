<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\CubicDecimeter;
use Cline\ValueObjects\Exceptions\InvalidDimensionsException;

describe('CubicDecimeter', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from meters', function (): void {
            $volume = CubicDecimeter::createFromMeter(1, 1, 1);

            expect($volume)->toBeInstanceOf(CubicDecimeter::class);
            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from meters as array', function (): void {
            $volume = CubicDecimeter::createFromMeterArray([
                'length' => 1,
                'width' => 1,
                'height' => 1,
            ]);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from decimeters', function (): void {
            $volume = CubicDecimeter::createFromDecimeter(10, 10, 10);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from decimeters as array', function (): void {
            $volume = CubicDecimeter::createFromDecimeterArray([
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

        test('creates from centimeters', function (): void {
            $volume = CubicDecimeter::createFromCentimeter(100, 100, 100);

            expect($volume->getVolume())->toBe(1_000.0);
            expect($volume->getLength())->toBe(10.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(10.0);
            expect($volume->toString())->toBe('1,000');
        });

        test('creates from centimeters as array', function (): void {
            $volume = CubicDecimeter::createFromCentimeterArray([
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
    });

    describe('Sad Paths', function (): void {
        test('throws exception for zero length', function (): void {
            CubicDecimeter::createFromMeter(0, 10, 10);
        })->throws(InvalidDimensionsException::class);

        test('throws exception for zero width', function (): void {
            CubicDecimeter::createFromMeter(10, 0, 10);
        })->throws(InvalidDimensionsException::class);

        test('throws exception for zero height', function (): void {
            CubicDecimeter::createFromMeter(10, 10, 0);
        })->throws(InvalidDimensionsException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles very small dimensions', function (): void {
            $volume = CubicDecimeter::createFromMeter(0.1, 0.1, 0.1);

            expect($volume->getVolume())->toBe(1.0);
            expect($volume->getLength())->toBe(1.0);
            expect($volume->getWidth())->toBe(1.0);
            expect($volume->getHeight())->toBe(1.0);
        });

        test('handles very large dimensions', function (): void {
            $volume = CubicDecimeter::createFromMeter(100, 100, 100);

            expect($volume->getVolume())->toBe(1_000_000_000.0);
            expect($volume->getLength())->toBe(1_000.0);
            expect($volume->getWidth())->toBe(1_000.0);
            expect($volume->getHeight())->toBe(1_000.0);
        });

        test('handles non-cubic dimensions', function (): void {
            $volume = CubicDecimeter::createFromCentimeter(50, 100, 200);

            expect($volume->getLength())->toBe(5.0);
            expect($volume->getWidth())->toBe(10.0);
            expect($volume->getHeight())->toBe(20.0);
            expect($volume->getVolume())->toBe(1_000.0);
        });

        test('handles fractional dimensions in decimeters', function (): void {
            $volume = CubicDecimeter::createFromDecimeter(5.5, 5.5, 5.5);

            expect($volume->getLength())->toBe(5.5);
            expect($volume->getWidth())->toBe(5.5);
            expect($volume->getHeight())->toBe(5.5);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
