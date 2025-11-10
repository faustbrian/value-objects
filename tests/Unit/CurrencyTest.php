<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Currency;
use Symfony\Component\Intl\Exception\MissingResourceException;

dataset('valid_currencies', [
    'USD' => ['USD', 'US Dollar', '$', 2, 840],
    'EUR' => ['EUR', 'Euro', '€', 2, 978],
    'GBP' => ['GBP', 'British Pound', '£', 2, 826],
    'JPY' => ['JPY', 'Japanese Yen', '¥', 0, 392],
]);

describe('Currency', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid currency codes', function (string $code, string $name, string $symbol, int $fractionDigits, int $numericCode): void {
            $currency = Currency::createFromString($code);

            expect($currency)->toBeInstanceOf(Currency::class);
            expect($currency->name)->toBe($name);
            expect($currency->symbol)->toBe($symbol);
            expect($currency->fractionDigits)->toBe($fractionDigits);
            expect($currency->numericCode)->toBe($numericCode);
            expect($currency->toString())->toBe($code);
        })->with('valid_currencies');

        test('creates USD currency', function (): void {
            $currency = Currency::createFromString('USD');

            expect($currency->name)->toBe('US Dollar');
            expect($currency->symbol)->toBe('$');
            expect($currency->fractionDigits)->toBe(2);
            expect($currency->roundingIncrement)->toBe(0);
            expect($currency->cashFractionDigits)->toBe(2);
            expect($currency->cashRoundingIncrement)->toBe(0);
            expect($currency->numericCode)->toBe(840);
            expect($currency->toString())->toBe('USD');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid currency code', function (): void {
            Currency::createFromString('XXX');
        })->throws(MissingResourceException::class);

        test('throws exception for lowercase currency code', function (): void {
            Currency::createFromString('usd');
        })->throws(MissingResourceException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles currency with zero fraction digits', function (): void {
            $currency = Currency::createFromString('JPY');

            expect($currency->fractionDigits)->toBe(0);
        });

        test('handles different currency symbols', function (): void {
            $usd = Currency::createFromString('USD');
            $eur = Currency::createFromString('EUR');

            expect($usd->symbol)->toBe('$');
            expect($eur->symbol)->toBe('€');
        });

        test('returns correct string representation', function (): void {
            $currency = Currency::createFromString('USD');

            expect($currency->toString())->toBe('USD');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
