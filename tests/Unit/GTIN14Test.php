<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GTIN14;

dataset('valid_gtin14_values', [
    'example 1' => ['12345678901231'],
    'GTIN.info example' => ['00012345600012'],
]);

dataset('invalid_gtin14_values', [
    'all zeros' => ['00000000000000'],
    'bad checksum' => ['12345678901232'],
    'too short' => ['1234567890123'],
    'non-numeric' => ['A1234567890123'],
    'contains dot' => ['12345.67890.1231'],
]);

describe('GTIN14', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GTIN-14 strings', function (string $value): void {
            $gtin = GTIN14::createFromString($value);

            expect($gtin)->toBeInstanceOf(GTIN14::class);
            expect($gtin->toString())->toBe($value);
        })->with('valid_gtin14_values');
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GTIN-14 strings', function (string $value): void {
            GTIN14::createFromString($value);
        })->with('invalid_gtin14_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GTIN14::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('rejects too long values', function (): void {
            GTIN14::createFromString('123456789012345');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): GTIN14 => GTIN14::createFromString('12345678901231'))->not->toThrow(Exception::class);
            expect(fn (): GTIN14 => GTIN14::createFromString('12345678901232'))->toThrow(InvalidArgumentException::class);
        });

        test('handles leading zeros', function (): void {
            $withZeros = GTIN14::createFromString('00012345600012');
            expect($withZeros->toString())->toBe('00012345600012');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
