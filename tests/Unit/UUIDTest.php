<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\UUID;
use Ramsey\Uuid\Uuid as RamseyUuid;

describe('UUID', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid UUID string', function (): void {
            $uuidString = RamseyUuid::uuid4()->toString();
            $uuid = UUID::createFromString($uuidString);

            expect($uuid->toString())->toBe($uuidString);
            expect((string) $uuid)->toBe($uuidString);
        });

        test('handles different UUID versions', function (): void {
            $uuid4 = RamseyUuid::uuid4()->toString();
            $vo4 = UUID::createFromString($uuid4);
            expect($vo4->toString())->toBe($uuid4);

            $uuid7 = RamseyUuid::uuid7()->toString();
            $vo7 = UUID::createFromString($uuid7);
            expect($vo7->toString())->toBe($uuid7);
        });

        test('preserves case', function (): void {
            $lowercase = '550e8400-e29b-41d4-a716-446655440000';
            $vo = UUID::createFromString($lowercase);
            expect($vo->toString())->toBe($lowercase);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid UUID string', function (): void {
            UUID::createFromString('invalid-uuid-string');
        })->throws(InvalidArgumentException::class);

        test('throws exception for malformed UUIDs', function (): void {
            UUID::createFromString('550e8400-e29b-41d4-a716');
        })->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            UUID::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('handles NIL UUID', function (): void {
            $nil = '00000000-0000-0000-0000-000000000000';
            $uuid = UUID::createFromString($nil);
            expect($uuid->toString())->toBe($nil);
        });

        test('string casting works', function (): void {
            $uuidString = RamseyUuid::uuid4()->toString();
            $uuid = UUID::createFromString($uuidString);

            expect((string) $uuid)->toBe($uuidString);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
