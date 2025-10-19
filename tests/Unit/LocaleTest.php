<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Locale;
use Symfony\Component\Intl\Exception\MissingResourceException;

dataset('valid_locales', [
    'US English' => ['en_US', 'English (United States)'],
    'Swedish' => ['sv_SE', 'Swedish (Sweden)'],
    'German' => ['de_DE', 'German (Germany)'],
    'French' => ['fr_FR', 'French (France)'],
    'UK English' => ['en_GB', 'English (United Kingdom)'],
]);

dataset('invalid_locales', [
    'invalid format' => ['invalid-locale'],
    'non-existent' => ['xx_XX'],
    'empty' => [''],
]);

describe('Locale', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid locale strings', function (string $locale, string $localized): void {
            $vo = Locale::createFromString($locale);

            expect($vo)->toBeInstanceOf(Locale::class);
            expect($vo->toString())->toBe($locale);
            expect($vo->localized)->toBe($localized);
        })->with('valid_locales');

        test('returns correct string representation', function (): void {
            $locale = Locale::createFromString('en_US');

            expect($locale->toString())->toBe('en_US');
        });

        test('returns correct localized representation', function (): void {
            $locale = Locale::createFromString('en_US');

            expect($locale->localized)->toBe('English (United States)');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid locale strings', function (string $locale): void {
            Locale::createFromString($locale);
        })->with('invalid_locales')->throws(MissingResourceException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles different locale formats', function (): void {
            $locales = ['en_US', 'fr_CA', 'de_AT', 'pt_BR', 'zh_CN'];

            foreach ($locales as $localeStr) {
                $locale = Locale::createFromString($localeStr);
                expect($locale->toString())->toBe($localeStr);
            }
        });

        test('handles regional variants', function (): void {
            $usEnglish = Locale::createFromString('en_US');
            $ukEnglish = Locale::createFromString('en_GB');

            expect($usEnglish->toString())->not->toBe($ukEnglish->toString());
            expect($usEnglish->localized)->not->toBe($ukEnglish->localized);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
