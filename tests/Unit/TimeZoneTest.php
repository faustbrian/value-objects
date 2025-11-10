<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Exceptions\Internationalization\InvalidTimeZoneException;
use Cline\ValueObjects\TimeZone;

dataset('valid_timezones', [
    'Helsinki' => ['Europe/Helsinki', 'Eastern European Time (Helsinki)'],
    'Stockholm' => ['Europe/Stockholm', 'Central European Time (Stockholm)'],
    'New York' => ['America/New_York', 'Eastern Time (New York)'],
    'Tokyo' => ['Asia/Tokyo', 'Japan Time (Tokyo)'],
]);

dataset('invalid_timezones', [
    'non-existent' => ['Invalid/TimeZone'],
    'empty' => [''],
    'wrong format' => ['InvalidTimeZone'],
]);

describe('TimeZone', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid timezone strings', function (string $timezone, string $localized): void {
            $tz = TimeZone::createFromString($timezone);

            expect($tz)->toBeInstanceOf(TimeZone::class);
            expect($tz->toString())->toBe($timezone);
            expect($tz->localized)->toBe($localized);
        })->with('valid_timezones');

        test('returns correct string representation', function (): void {
            $tz = TimeZone::createFromString('Europe/Helsinki');

            expect($tz->toString())->toBe('Europe/Helsinki');
        });

        test('returns correct localized representation', function (): void {
            $tz = TimeZone::createFromString('Europe/Helsinki');

            expect($tz->localized)->toBe('Eastern European Time (Helsinki)');
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid timezone strings', function (string $timezone): void {
            TimeZone::createFromString($timezone);
        })->with('invalid_timezones')->throws(InvalidTimeZoneException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles various timezone regions', function (): void {
            $timezones = [
                'Europe/London',
                'America/Los_Angeles',
                'Asia/Shanghai',
                'Australia/Sydney',
                'Africa/Cairo',
            ];

            foreach ($timezones as $tz) {
                $timezone = TimeZone::createFromString($tz);
                expect($timezone->toString())->toBe($tz);
            }
        });

        test('handles GMT timezone', function (): void {
            $gmt = TimeZone::createFromString('Europe/London');

            expect($gmt->toString())->toBe('Europe/London');
        });

        test('handles underscores in city names', function (): void {
            $tz = TimeZone::createFromString('America/New_York');

            expect($tz->toString())->toBe('America/New_York');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
