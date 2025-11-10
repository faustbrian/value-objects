<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\TagName;

describe('TagName', function (): void {
    describe('Happy Paths', function (): void {
        test('creates valid tag name', function (): void {
            $tagName = new TagName('test tag');

            expect($tagName)->toBeInstanceOf(TagName::class);
            expect($tagName->value)->toBe('test tag');
            expect($tagName->toString())->toBe('test tag');
            expect((string) $tagName)->toBe('test tag');
        });

        test('compares identical tag names as equal', function (): void {
            $tagName1 = new TagName('test');
            $tagName2 = new TagName('test');

            expect($tagName1->equals($tagName2))->toBeTrue();
        });

        test('compares different tag names as not equal', function (): void {
            $tagName1 = new TagName('test');
            $tagName2 = new TagName('different');

            expect($tagName1->equals($tagName2))->toBeFalse();
        });
    });

    describe('Sad Paths', function (): void {
        test('rejects empty tag name', function (): void {
            new TagName('');
        })->throws(InvalidArgumentException::class, 'Tag name cannot be empty');

        test('rejects whitespace-only tag name', function (): void {
            new TagName('   ');
        })->throws(InvalidArgumentException::class, 'Tag name cannot be empty');

        test('rejects tag name exceeding 255 characters', function (): void {
            $longName = str_repeat('a', 256);

            new TagName($longName);
        })->throws(InvalidArgumentException::class, 'Tag name cannot exceed 255 characters');
    });

    describe('Edge Cases', function (): void {
        test('handles tag name at maximum length', function (): void {
            $maxName = str_repeat('a', 255);
            $tagName = new TagName($maxName);

            expect($tagName->value)->toBe($maxName);
            expect(mb_strlen($tagName->value))->toBe(255);
        });

        test('handles tag names with special characters', function (): void {
            $tagName = new TagName('tag-name_123');

            expect($tagName->value)->toBe('tag-name_123');
        });

        test('handles tag names with spaces', function (): void {
            $tagName = new TagName('multiple word tag');

            expect($tagName->value)->toBe('multiple word tag');
        });

        test('string casting works correctly', function (): void {
            $tagName = new TagName('castable');

            expect((string) $tagName)->toBe('castable');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
