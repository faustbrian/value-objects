<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\PostalCode;

dataset('postal_codes_by_country', [
    'Finland' => ['12345', 'FI', '12345'],
    'Sweden' => ['12345', 'SE', '123 45'],
    'Latvia' => ['1234', 'LV', 'LV-1234'],
    'Lithuania' => ['12345', 'LT', '12345'],
    'Estonia' => ['12345', 'EE', '12345'],
]);

describe('PostalCode', function (): void {
    describe('Happy Paths', function (): void {
        test('formats postal codes by country', function (string $code, string $country, string $expected): void {
            $postalCode = PostalCode::createFromString($code, $country);

            expect($postalCode)->toBeInstanceOf(PostalCode::class);
            expect($postalCode->toString())->toBe($expected);
        })->with('postal_codes_by_country');

        test('creates Finnish postal code', function (): void {
            $postalCode = PostalCode::createFromString('12345', 'FI');

            expect($postalCode->toString())->toBe('12345');
        });

        test('creates Swedish postal code with space', function (): void {
            $postalCode = PostalCode::createFromString('12345', 'SE');

            expect($postalCode->toString())->toBe('123 45');
        });

        test('creates Latvian postal code with prefix', function (): void {
            $postalCode = PostalCode::createFromString('1234', 'LV');

            expect($postalCode->toString())->toBe('LV-1234');
        });
    });

    describe('Sad Paths', function (): void {
        // Add when validation is implemented
    });

    describe('Edge Cases', function (): void {
        test('handles different postal code lengths', function (): void {
            $fi = PostalCode::createFromString('12345', 'FI');
            $lv = PostalCode::createFromString('1234', 'LV');

            expect(mb_strlen($fi->toString()))->toBe(5);
            expect(mb_strlen($lv->toString()))->toBe(7);
        });

        test('handles country-specific formatting', function (): void {
            $unformatted = PostalCode::createFromString('12345', 'FI');
            $formatted = PostalCode::createFromString('12345', 'SE');

            expect($unformatted->toString())->not->toContain(' ');
            expect($formatted->toString())->toContain(' ');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
