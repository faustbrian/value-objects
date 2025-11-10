<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Country;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidCountryCodeException;

dataset('valid_country_codes', [
    'US' => ['US', 'USA', 'United States'],
    'SE' => ['SE', 'SWE', 'Sweden'],
    'GB' => ['GB', 'GBR', 'United Kingdom'],
    'DE' => ['DE', 'DEU', 'Germany'],
    'FR' => ['FR', 'FRA', 'France'],
    'JP' => ['JP', 'JPN', 'Japan'],
]);

describe('Country', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid country code string', function (string $alpha2, string $alpha3, string $localized): void {
            $country = Country::createFromString($alpha2);

            expect($country->alpha2)->toBe($alpha2);
            expect($country->alpha3)->toBe($alpha3);
            expect($country->localized)->toBe($localized);
            expect($country->toString())->toBe($alpha2);
        })->with('valid_country_codes');

        test('returns correct alpha-2 code', function (): void {
            $country = Country::createFromString('US');

            expect($country->alpha2)->toBe('US');
        });

        test('returns correct alpha-3 code', function (): void {
            $country = Country::createFromString('US');

            expect($country->alpha3)->toBe('USA');
        });

        test('returns correct localized name', function (): void {
            $country = Country::createFromString('US');

            expect($country->localized)->toBe('United States');
        });

        test('returns correct string representation', function (): void {
            $country = Country::createFromString('US');

            expect($country->toString())->toBe('US');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid country code', function (): void {
            Country::createFromString('XX');
        })->throws(InvalidCountryCodeException::class);

        test('throws exception for empty string', function (): void {
            Country::createFromString('');
        })->throws(InvalidCountryCodeException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles less common countries', function (): void {
            $iceland = Country::createFromString('IS');
            expect($iceland->alpha2)->toBe('IS');
            expect($iceland->alpha3)->toBe('ISL');

            $switzerland = Country::createFromString('CH');
            expect($switzerland->alpha2)->toBe('CH');
            expect($switzerland->alpha3)->toBe('CHE');
        });

        test('rejects lowercase codes', function (): void {
            Country::createFromString('us');
        })->throws(InvalidCountryCodeException::class);

        test('rejects alpha-3 codes', function (): void {
            Country::createFromString('USA');
        })->throws(InvalidCountryCodeException::class);

        test('rejects numeric codes', function (): void {
            Country::createFromString('840');
        })->throws(InvalidCountryCodeException::class);
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
