<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\TagType;

describe('TagType', function (): void {
    describe('Happy Paths', function (): void {
        test('creates valid tag type', function (): void {
            $tagType = new TagType('category');

            expect($tagType)->toBeInstanceOf(TagType::class);
            expect($tagType->value)->toBe('category');
            expect($tagType->toString())->toBe('category');
            expect((string) $tagType)->toBe('category');
            expect($tagType->isDefault())->toBeFalse();
        });

        test('creates default tag type without arguments', function (): void {
            $tagType = new TagType();

            expect($tagType->value)->toBeNull();
            expect($tagType->toString())->toBeNull();
            expect((string) $tagType)->toBe('');
            expect($tagType->isDefault())->toBeTrue();
        });

        test('creates default tag type with explicit null', function (): void {
            $tagType = new TagType();

            expect($tagType->value)->toBeNull();
            expect($tagType->isDefault())->toBeTrue();
        });

        test('compares identical tag types as equal', function (): void {
            $tagType1 = new TagType('category');
            $tagType2 = new TagType('category');

            expect($tagType1->equals($tagType2))->toBeTrue();
        });

        test('compares different tag types as not equal', function (): void {
            $tagType1 = new TagType('category');
            $tagType2 = new TagType('topic');

            expect($tagType1->equals($tagType2))->toBeFalse();
        });

        test('compares default tag types as equal', function (): void {
            $tagType1 = new TagType();
            $tagType2 = new TagType();

            expect($tagType1->equals($tagType2))->toBeTrue();
        });
    });

    describe('Sad Paths', function (): void {
        test('rejects empty string tag type', function (): void {
            new TagType('');
        })->throws(InvalidArgumentException::class, 'Tag type cannot be empty string');

        test('rejects whitespace-only tag type', function (): void {
            new TagType('   ');
        })->throws(InvalidArgumentException::class, 'Tag type cannot be empty string');

        test('rejects tag type exceeding 100 characters', function (): void {
            $longType = str_repeat('a', 101);

            new TagType($longType);
        })->throws(InvalidArgumentException::class, 'Tag type cannot exceed 100 characters');
    });

    describe('Edge Cases', function (): void {
        test('handles tag type at maximum length', function (): void {
            $maxType = str_repeat('a', 100);
            $tagType = new TagType($maxType);

            expect($tagType->value)->toBe($maxType);
            expect(mb_strlen((string) $tagType->value))->toBe(100);
        });

        test('handles default tag type compared to regular type', function (): void {
            $default = new TagType();
            $regular = new TagType('category');

            expect($default->equals($regular))->toBeFalse();
        });

        test('string casting works for regular type', function (): void {
            $tagType = new TagType('category');

            expect((string) $tagType)->toBe('category');
        });

        test('string casting works for default type', function (): void {
            $tagType = new TagType();

            expect((string) $tagType)->toBe('');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
