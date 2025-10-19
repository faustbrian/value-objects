<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GTIN8;

dataset('valid_gtin8_values', [
    'Wikipedia example 1' => ['42345671'],
    'Wikipedia example 2 with separator' => ['4719-5127'],
    'Wikipedia example 3 with separator' => ['9638-5074'],
]);

dataset('invalid_gtin8_values', [
    'all zeros' => ['00000000'],
    'bad checksum' => ['42345670'],
    'too long' => ['423456712'],
    'non-numeric' => ['12345671'],
    'contains dot' => ['4234.5671'],
]);

describe('GTIN8', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GTIN-8 strings', function (string $value): void {
            $gtin = GTIN8::createFromString($value);

            expect($gtin)->toBeInstanceOf(GTIN8::class);
            expect($gtin->toString())->toBe($value);
        })->with('valid_gtin8_values');

        test('preserves separator format', function (): void {
            $withSeparator = GTIN8::createFromString('4719-5127');

            expect($withSeparator->toString())->toBe('4719-5127');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GTIN-8 strings', function (string $value): void {
            GTIN8::createFromString($value);
        })->with('invalid_gtin8_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GTIN8::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('rejects too short values', function (): void {
            GTIN8::createFromString('1234567');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): GTIN8 => GTIN8::createFromString('42345671'))->not->toThrow(Exception::class);
            expect(fn (): GTIN8 => GTIN8::createFromString('42345670'))->toThrow(InvalidArgumentException::class);
        });

        test('handles various separator formats', function (): void {
            $withDash = GTIN8::createFromString('4719-5127');
            expect($withDash->toString())->toBe('4719-5127');

            $noSeparator = GTIN8::createFromString('42345671');
            expect($noSeparator->toString())->toBe('42345671');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
