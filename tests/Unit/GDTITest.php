<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GDTI;

dataset('valid_gdti_values', [
    'with spaces' => ['4719512002889 1234567890 123456'],
    'with dashes' => ['4719512002889-1234567890-123456'],
    'with spaces 2' => ['4719512002889 1234567890 123456'],
]);

dataset('invalid_gdti_values', [
    'all zeros GTIN' => ['0000000000000 1234567890 123456'],
    'too short GTIN' => ['471951200288-1234567890-123456'],
    'no separator integer' => ['4719512002881234567890123456'],
    'bad checksum' => ['4719512002888-1234567890-123456'],
    'contains dot' => ['4719512002889.1234567890.123456'],
]);

describe('GDTI', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GDTI strings', function (string $value): void {
            $gdti = GDTI::createFromString($value);

            expect($gdti)->toBeInstanceOf(GDTI::class);
            expect($gdti->toString())->toBe($value);
        })->with('valid_gdti_values');

        test('preserves separator format', function (): void {
            $withSpaces = GDTI::createFromString('4719512002889 1234567890 123456');
            expect($withSpaces->toString())->toBe('4719512002889 1234567890 123456');

            $withDashes = GDTI::createFromString('4719512002889-1234567890-123456');
            expect($withDashes->toString())->toBe('4719512002889-1234567890-123456');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GDTI strings', function (string $value): void {
            GDTI::createFromString($value);
        })->with('invalid_gdti_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GDTI::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('validates GTIN-13 component checksum', function (): void {
            expect(fn (): GDTI => GDTI::createFromString('4719512002889 1234567890 123456'))->not->toThrow(Exception::class);
            expect(fn (): GDTI => GDTI::createFromString('4719512002888 1234567890 123456'))->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
