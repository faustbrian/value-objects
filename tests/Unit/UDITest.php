<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\UDI;

dataset('valid_udi_values', [
    'AccessGUDID example 1' => ['07610221010301'],
    'AccessGUDID example 2' => ['10887488125541'],
    'AccessGUDID example 3' => ['00868866000011'],
    'AccessGUDID with spaces' => ['1038178 0064596'],
]);

dataset('invalid_udi_values', [
    'all zeros' => ['0000000000000'],
    'bad checksum' => ['10381780064595'],
    'too short 1' => ['1038178006459'],
    'too short 2' => ['0761022101030'],
    'non-numeric' => ['0761022101030A'],
    'contains dot' => ['1038178.0064596'],
]);

describe('UDI', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid UDI strings', function (string $value): void {
            $udi = UDI::createFromString($value);

            expect($udi)->toBeInstanceOf(UDI::class);
            expect($udi->toString())->toBe($value);
        })->with('valid_udi_values');

        test('preserves space separator', function (): void {
            $withSpaces = UDI::createFromString('1038178 0064596');
            expect($withSpaces->toString())->toBe('1038178 0064596');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid UDI strings', function (string $value): void {
            UDI::createFromString($value);
        })->with('invalid_udi_values')->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            UDI::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('validates checksum digit correctly', function (): void {
            expect(fn (): UDI => UDI::createFromString('10381780064596'))->not->toThrow(Exception::class);
            expect(fn (): UDI => UDI::createFromString('10381780064595'))->toThrow(InvalidArgumentException::class);
        });

        test('handles medical device identifiers', function (): void {
            $deviceId = UDI::createFromString('07610221010301');
            expect($deviceId->toString())->toBe('07610221010301');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
