<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GTIN13;

dataset('valid_gtin13_values', [
    'example 1' => ['4006381333931'],
    'example 2' => ['5901234123457'],
    'ISBN example' => ['9780201379624'],
    'example 4' => ['9310779300005'],
]);

dataset('invalid_gtin13_values', [
    'incorrect checksum 1' => ['4006381333932'],
    'incorrect checksum 2' => ['4601234567890'],
    'incorrect checksum 3' => ['5901234123456'],
    'arbitrary incorrect' => ['1234567890123'],
]);

describe('GTIN13', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GTIN-13 strings', function (string $value): void {
            $gtin = GTIN13::createFromString($value);

            expect($gtin)->toBeInstanceOf(GTIN13::class);
            expect($gtin->toString())->toBe($value);
        })->with('valid_gtin13_values');
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GTIN-13 strings', function (string $value): void {
            GTIN13::createFromString($value);
        })->with('invalid_gtin13_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GTIN13::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('rejects all zeros', function (): void {
            GTIN13::createFromString('0000000000000');
        })->throws(InvalidArgumentException::class);

        test('rejects too short values', function (): void {
            GTIN13::createFromString('123456789012');
        })->throws(InvalidArgumentException::class);

        test('rejects too long values', function (): void {
            GTIN13::createFromString('12345678901234');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): GTIN13 => GTIN13::createFromString('4006381333931'))->not->toThrow(Exception::class);
            expect(fn (): GTIN13 => GTIN13::createFromString('4006381333932'))->toThrow(InvalidArgumentException::class);
        });

        test('handles ISBN-13 barcodes', function (): void {
            $isbn = GTIN13::createFromString('9780201379624');
            expect($isbn->toString())->toBe('9780201379624');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
