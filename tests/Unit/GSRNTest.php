<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GSRN;

dataset('valid_gsrn_values', [
    'no separator' => ['735005385000000011'],
    'with spaces' => ['735005385 00000001 1'],
    'with dashes' => ['735005385-00000001-1'],
]);

dataset('invalid_gsrn_values', [
    'too short' => ['73500538500000001'],
    'too long' => ['735005385-000000001-1'],
    'non-numeric' => ['735005385-A0000001-1'],
    'bad checksum' => ['735005385-00000001-2'],
    'contains dot' => ['735005385-00000001.1'],
]);

describe('GSRN', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GSRN-18 strings', function (string $value): void {
            $gsrn = GSRN::createFromString($value);

            expect($gsrn)->toBeInstanceOf(GSRN::class);
            expect($gsrn->toString())->toBe($value);
        })->with('valid_gsrn_values');

        test('preserves separator format', function (): void {
            $withSpaces = GSRN::createFromString('735005385 00000001 1');
            expect($withSpaces->toString())->toBe('735005385 00000001 1');

            $withDashes = GSRN::createFromString('735005385-00000001-1');
            expect($withDashes->toString())->toBe('735005385-00000001-1');

            $noSeparator = GSRN::createFromString('735005385000000011');
            expect($noSeparator->toString())->toBe('735005385000000011');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GSRN-18 strings', function (string $value): void {
            GSRN::createFromString($value);
        })->with('invalid_gsrn_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GSRN::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): GSRN => GSRN::createFromString('735005385000000011'))->not->toThrow(Exception::class);
            expect(fn (): GSRN => GSRN::createFromString('735005385000000012'))->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
