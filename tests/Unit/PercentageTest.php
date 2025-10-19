<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Percentage;

describe('Percentage', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from number with default format', function (): void {
            $percentage = Percentage::createFromNumber(10);

            expect($percentage)->toBeInstanceOf(Percentage::class);
            expect($percentage->toString())->toBe('10%');
        });

        test('creates with specified precision', function (): void {
            $percentage = Percentage::createFromNumber(10, 2);

            expect($percentage->toString())->toBe('10.00%');
        });

        test('creates with max precision', function (): void {
            $percentage = Percentage::createFromNumber(10.123_456_789, 10, 5);

            expect($percentage->toString())->toBe('10.12346%');
        });

        test('creates with locale formatting', function (): void {
            $percentage = Percentage::createFromNumber(10, 0, null, 'fi_FI');

            expect($percentage->toString())->toMatchSnapshot();
        });

        test('handles zero percentage', function (): void {
            $percentage = Percentage::createFromNumber(0);

            expect($percentage->toString())->toBe('0%');
        });

        test('handles 100 percentage', function (): void {
            $percentage = Percentage::createFromNumber(100);

            expect($percentage->toString())->toBe('100%');
        });
    });

    describe('Sad Paths', function (): void {
        test('handles negative percentages', function (): void {
            $percentage = Percentage::createFromNumber(-10);

            expect($percentage->toString())->toBe('-10%');
        });

        test('handles percentages over 100', function (): void {
            $percentage = Percentage::createFromNumber(150);

            expect($percentage->toString())->toBe('150%');
        });
    });

    describe('Edge Cases', function (): void {
        test('handles very small percentages', function (): void {
            $percentage = Percentage::createFromNumber(0.001, 3);

            expect($percentage->toString())->toBe('0.001%');
        });

        test('handles very large percentages', function (): void {
            $percentage = Percentage::createFromNumber(999_999);

            expect($percentage->toString())->toBe('999,999%');
        });

        test('handles decimal rounding', function (): void {
            $percentage = Percentage::createFromNumber(10.666_666_666, 2);

            expect($percentage->toString())->toBe('10.67%');
        });

        test('handles various precision levels', function (): void {
            $value = 33.333_333_333;

            $p0 = Percentage::createFromNumber($value, 0);
            $p2 = Percentage::createFromNumber($value, 2);
            $p4 = Percentage::createFromNumber($value, 4);

            expect($p0->toString())->toBe('33%');
            expect($p2->toString())->toBe('33.33%');
            expect($p4->toString())->toBe('33.3333%');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
