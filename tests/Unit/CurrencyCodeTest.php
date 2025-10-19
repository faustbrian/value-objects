<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\CurrencyCode;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidCurrencyCodeException;

dataset('valid_currency_codes', [
    'USD' => ['USD', 'US Dollar'],
    'EUR' => ['EUR', 'Euro'],
    'GBP' => ['GBP', 'British Pound'],
    'JPY' => ['JPY', 'Japanese Yen'],
    'SEK' => ['SEK', 'Swedish Krona'],
    'CHF' => ['CHF', 'Swiss Franc'],
]);

describe('CurrencyCode', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid currency code string', function (string $code, string $localized): void {
            $currency = CurrencyCode::createFromString($code);

            expect($currency->toString())->toBe($code);
            expect($currency->localized)->toBe($localized);
        })->with('valid_currency_codes');

        test('returns correct string representation', function (): void {
            $currency = CurrencyCode::createFromString('USD');

            expect($currency->toString())->toBe('USD');
        });

        test('returns correct localized name', function (): void {
            $currency = CurrencyCode::createFromString('USD');

            expect($currency->localized)->toBe('US Dollar');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid currency code', function (): void {
            CurrencyCode::createFromString('XXX');
        })->throws(InvalidCurrencyCodeException::class);

        test('throws exception for empty string', function (): void {
            CurrencyCode::createFromString('');
        })->throws(InvalidCurrencyCodeException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles currencies with zero decimal places', function (): void {
            $jpy = CurrencyCode::createFromString('JPY');
            expect($jpy->toString())->toBe('JPY');
            expect($jpy->localized)->toBe('Japanese Yen');
        });

        test('handles currencies with three decimal places', function (): void {
            $bhd = CurrencyCode::createFromString('BHD');
            expect($bhd->toString())->toBe('BHD');
            expect($bhd->localized)->toBe('Bahraini Dinar');

            $kwd = CurrencyCode::createFromString('KWD');
            expect($kwd->toString())->toBe('KWD');
            expect($kwd->localized)->toBe('Kuwaiti Dinar');
        });

        test('rejects lowercase codes', function (): void {
            CurrencyCode::createFromString('usd');
        })->throws(InvalidCurrencyCodeException::class);

        test('rejects too short codes', function (): void {
            CurrencyCode::createFromString('US');
        })->throws(InvalidCurrencyCodeException::class);

        test('rejects too long codes', function (): void {
            CurrencyCode::createFromString('USDD');
        })->throws(InvalidCurrencyCodeException::class);

        test('rejects numeric codes', function (): void {
            CurrencyCode::createFromString('123');
        })->throws(InvalidCurrencyCodeException::class);
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
