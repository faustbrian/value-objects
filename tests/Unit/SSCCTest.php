<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\SSCC;

dataset('valid_sscc_values', [
    'GS1 PDF example' => ['806141411234567896'],
    'Morovia example' => ['007189085627231896'],
    'BarcodeRobot example' => ['054100001234567897'],
    'ActiveBarcode example' => ['340123450000000000'],
]);

dataset('invalid_sscc_values', [
    'all zeros' => ['000000000000000000'],
    'bad checksum' => ['806141411234567897'],
    'too long' => ['8061414112345678961'],
    'non-numeric' => ['A06141411234567896'],
    'contains dot' => ['806141411.2345678961'],
]);

describe('SSCC', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid SSCC-18 strings', function (string $value): void {
            $sscc = SSCC::createFromString($value);

            expect($sscc)->toBeInstanceOf(SSCC::class);
            expect($sscc->toString())->toBe($value);
        })->with('valid_sscc_values');
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid SSCC-18 strings', function (string $value): void {
            SSCC::createFromString($value);
        })->with('invalid_sscc_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            SSCC::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): SSCC => SSCC::createFromString('806141411234567896'))->not->toThrow(Exception::class);
            expect(fn (): SSCC => SSCC::createFromString('806141411234567897'))->toThrow(InvalidArgumentException::class);
        });

        test('handles leading zeros', function (): void {
            $withZeros = SSCC::createFromString('007189085627231896');
            expect($withZeros->toString())->toBe('007189085627231896');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
