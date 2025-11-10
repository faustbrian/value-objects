<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GTIN12;

dataset('valid_gtin12_values', [
    'GS1 example' => ['614141000036'],
    'Wikipedia UPC example' => ['1-23456-78999-9'],
]);

dataset('invalid_gtin12_values', [
    'all zeros' => ['000000000000'],
    'bad checksum' => ['614141000037'],
    'too short' => ['61414100003'],
    'non-numeric' => ['A14141000036'],
    'contains dot' => ['1.23456.78999.9'],
]);

describe('GTIN12', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GTIN-12 strings', function (string $value): void {
            $gtin = GTIN12::createFromString($value);

            expect($gtin)->toBeInstanceOf(GTIN12::class);
            expect($gtin->toString())->toBe($value);
        })->with('valid_gtin12_values');

        test('preserves separator format', function (): void {
            $withSeparator = GTIN12::createFromString('1-23456-78999-9');

            expect($withSeparator->toString())->toBe('1-23456-78999-9');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GTIN-12 strings', function (string $value): void {
            GTIN12::createFromString($value);
        })->with('invalid_gtin12_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GTIN12::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('rejects too long values', function (): void {
            GTIN12::createFromString('6141410000361');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): GTIN12 => GTIN12::createFromString('614141000036'))->not->toThrow(Exception::class);
            expect(fn (): GTIN12 => GTIN12::createFromString('614141000037'))->toThrow(InvalidArgumentException::class);
        });

        test('handles various separator formats', function (): void {
            $withDash = GTIN12::createFromString('1-23456-78999-9');
            expect($withDash->toString())->toBe('1-23456-78999-9');

            $noSeparator = GTIN12::createFromString('614141000036');
            expect($noSeparator->toString())->toBe('614141000036');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
