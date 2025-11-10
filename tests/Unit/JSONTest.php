<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Exceptions\Serialization\NotJsonException;
use Cline\ValueObjects\JSON;

describe('JSON', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid JSON string', function (): void {
            $jsonString = '{"foo":"bar"}';
            $json = JSON::createFromString($jsonString);

            expect($json)->toBeInstanceOf(JSON::class);
            expect($json->encoded)->toBe($jsonString);
        });

        test('creates from JSON object', function (): void {
            $jsonString = '{"name":"John","age":30}';
            $json = JSON::createFromString($jsonString);

            expect($json->encoded)->toBe($jsonString);
        });

        test('creates from JSON array', function (): void {
            $jsonString = '[1,2,3]';
            $json = JSON::createFromString($jsonString);

            expect($json->encoded)->toBe($jsonString);
        });

        test('creates from empty JSON object', function (): void {
            $jsonString = '{}';
            $json = JSON::createFromString($jsonString);

            expect($json->encoded)->toBe($jsonString);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid JSON', function (): void {
            JSON::createFromString('{"foo":bar}');
        })->throws(NotJsonException::class);

        test('throws exception for malformed JSON', function (): void {
            JSON::createFromString('{invalid}');
        })->throws(NotJsonException::class);

        test('throws exception for unclosed JSON', function (): void {
            JSON::createFromString('{"foo":"bar"');
        })->throws(NotJsonException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles nested JSON objects', function (): void {
            $jsonString = '{"user":{"name":"John","address":{"city":"NYC"}}}';
            $json = JSON::createFromString($jsonString);

            expect($json->encoded)->toBe($jsonString);
        });

        test('handles JSON with special characters', function (): void {
            $jsonString = '{"text":"Hello\nWorld"}';
            $json = JSON::createFromString($jsonString);

            expect($json->encoded)->toContain('Hello');
        });

        test('handles JSON with unicode', function (): void {
            $jsonString = '{"emoji":"😀"}';
            $json = JSON::createFromString($jsonString);

            expect($json->encoded)->toContain('emoji');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
