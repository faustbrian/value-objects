<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GLN;

dataset('valid_gln_values', [
    'GS1 example 1' => ['0614141000012'],
    'GS1 example 2' => ['0614141000029'],
    'GS1 example 3' => ['0614141000036'],
    'with spaces' => ['0614141 00002 9'],
    'with dashes' => ['0614141-00003-6'],
]);

dataset('invalid_gln_values', [
    'all zeros' => ['0000000000000'],
    'too short' => ['061414100001'],
    'non-numeric' => ['A614141000016'],
    'bad checksum' => ['0614141000015'],
    'contains dot' => ['0614141.00001.6'],
]);

describe('GLN', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GLN strings', function (string $value): void {
            $gln = GLN::createFromString($value);

            expect($gln)->toBeInstanceOf(GLN::class);
            expect($gln->toString())->toBe($value);
        })->with('valid_gln_values');

        test('preserves separator format', function (): void {
            $withSpaces = GLN::createFromString('0614141 00002 9');
            expect($withSpaces->toString())->toBe('0614141 00002 9');

            $withDashes = GLN::createFromString('0614141-00003-6');
            expect($withDashes->toString())->toBe('0614141-00003-6');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GLN strings', function (string $value): void {
            GLN::createFromString($value);
        })->with('invalid_gln_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GLN::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): GLN => GLN::createFromString('0614141000012'))->not->toThrow(Exception::class);
            expect(fn (): GLN => GLN::createFromString('0614141000015'))->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
