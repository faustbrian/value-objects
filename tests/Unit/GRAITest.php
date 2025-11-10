<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GRAI;

dataset('valid_grai_values', [
    'with spaces and alphanumeric' => ['04719512002889 1234567890 12345A'],
    'with dashes' => ['04719512002889-1234567890-123456'],
    'with spaces' => ['04719512002889 1234567890 123456'],
    'beer keg example 1' => ['012345678900051234AX01'],
    'beer keg example 2' => ['012345678900051234AX02'],
]);

dataset('invalid_grai_values', [
    'all zeros GTIN' => ['0000000000000 1234567890 12345A'],
    'no separator integer' => ['4719512002881234567890123456'],
    'bad checksum' => ['04719512002888-1234567890-123456'],
    'contains dot' => ['04719512002889.1234567890.123456'],
    'invalid serial char' => ['0471951200288-1234567890-12345;'],
]);

describe('GRAI', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid GRAI strings', function (string $value): void {
            $grai = GRAI::createFromString($value);

            expect($grai)->toBeInstanceOf(GRAI::class);
            expect($grai->toString())->toBe($value);
        })->with('valid_grai_values');

        test('handles alphanumeric serial numbers', function (): void {
            $alphanumeric = GRAI::createFromString('04719512002889 1234567890 12345A');
            expect($alphanumeric->toString())->toBe('04719512002889 1234567890 12345A');
        });

        test('preserves separator format', function (): void {
            $withSpaces = GRAI::createFromString('04719512002889 1234567890 123456');
            expect($withSpaces->toString())->toBe('04719512002889 1234567890 123456');

            $withDashes = GRAI::createFromString('04719512002889-1234567890-123456');
            expect($withDashes->toString())->toBe('04719512002889-1234567890-123456');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid GRAI strings', function (string $value): void {
            GRAI::createFromString($value);
        })->with('invalid_grai_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            GRAI::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('validates GTIN component checksum', function (): void {
            expect(fn (): GRAI => GRAI::createFromString('04719512002889 1234567890 123456'))->not->toThrow(Exception::class);
            expect(fn (): GRAI => GRAI::createFromString('04719512002888 1234567890 123456'))->toThrow(InvalidArgumentException::class);
        });

        test('handles reusable asset identifiers', function (): void {
            $keg = GRAI::createFromString('012345678900051234AX01');
            expect($keg->toString())->toBe('012345678900051234AX01');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
