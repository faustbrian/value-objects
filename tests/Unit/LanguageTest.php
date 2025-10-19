<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Exceptions\Internationalization\InvalidLanguageCodeException;
use Cline\ValueObjects\Language;

dataset('valid_language_codes', [
    'English' => ['en', 'English'],
    'Swedish' => ['sv', 'Swedish'],
    'German' => ['de', 'German'],
    'French' => ['fr', 'French'],
    'Spanish' => ['es', 'Spanish'],
]);

dataset('invalid_language_codes', [
    'non-existent' => ['xx'],
    'too long' => ['eng'],
    'numeric' => ['12'],
    'empty' => [''],
]);

describe('Language', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid language codes', function (string $code, string $localized): void {
            $language = Language::createFromString($code);

            expect($language)->toBeInstanceOf(Language::class);
            expect($language->toString())->toBe($code);
            expect($language->localized)->toBe($localized);
        })->with('valid_language_codes');

        test('returns correct string representation', function (): void {
            $language = Language::createFromString('en');

            expect($language->toString())->toBe('en');
        });

        test('returns correct localized representation', function (): void {
            $language = Language::createFromString('en');

            expect($language->localized)->toBe('English');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid language codes', function (string $code): void {
            Language::createFromString($code);
        })->with('invalid_language_codes')->throws(InvalidLanguageCodeException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles case sensitivity', function (): void {
            $language = Language::createFromString('en');

            expect($language->toString())->toBe('en');
        });

        test('handles all ISO 639-1 two-letter codes', function (): void {
            $codes = ['en', 'fr', 'de', 'es', 'it', 'pt', 'ru', 'ja', 'zh', 'ar'];

            foreach ($codes as $code) {
                $language = Language::createFromString($code);
                expect($language->toString())->toBe($code);
            }
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
