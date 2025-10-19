<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\ULID;
use Illuminate\Support\Sleep;
use Symfony\Component\Uid\Ulid as Symfony;

describe('ULID', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid ULID string', function (): void {
            $validUlid = Symfony::generate();
            $ulid = ULID::createFromString($validUlid);

            expect($ulid->toString())->toBe($validUlid);
        });

        test('handles multiple generated ULIDs', function (): void {
            $ulid1 = Symfony::generate();
            $ulid2 = Symfony::generate();

            $vo1 = ULID::createFromString($ulid1);
            $vo2 = ULID::createFromString($ulid2);

            expect($vo1->toString())->toBe($ulid1);
            expect($vo2->toString())->toBe($ulid2);
            expect($vo1->toString())->not->toBe($vo2->toString());
        });

        test('returns correct string representation', function (): void {
            $validUlid = Symfony::generate();
            $ulid = ULID::createFromString($validUlid);

            expect($ulid->toString())->toBe($validUlid);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid ULID string', function (): void {
            ULID::createFromString('invalid-ulid');
        })->throws(InvalidArgumentException::class);

        test('throws exception for malformed ULIDs', function (): void {
            ULID::createFromString('01ARZ3NDEKTSV4RRFFQ69G5FA');
        })->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('rejects empty string', function (): void {
            ULID::createFromString('');
        })->throws(InvalidArgumentException::class);

        test('handles lexicographically sortable property', function (): void {
            $ulid1 = Symfony::generate();
            Sleep::usleep(1_000);
            $ulid2 = Symfony::generate();

            $vo1 = ULID::createFromString($ulid1);
            $vo2 = ULID::createFromString($ulid2);

            expect($vo1->toString() < $vo2->toString())->toBeTrue();
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
