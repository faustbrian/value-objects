<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Exceptions\Serialization\NotYamlException;
use Cline\ValueObjects\YAML;

describe('YAML', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid YAML string', function (): void {
            $yamlString = 'foo: bar';
            $yaml = YAML::createFromString($yamlString);

            expect($yaml)->toBeInstanceOf(YAML::class);
            expect($yaml->decoded)->toBe(['foo' => 'bar']);
            expect($yaml->encoded)->toBe($yamlString);
        });

        test('string casting returns encoded string', function (): void {
            $yamlString = 'foo: bar';
            $yaml = YAML::createFromString($yamlString);

            expect((string) $yaml)->toBe($yamlString);
        });

        test('creates from YAML with nested structure', function (): void {
            $yamlString = "parent:\n  child: value";
            $yaml = YAML::createFromString($yamlString);

            expect($yaml->decoded)->toBe(['parent' => ['child' => 'value']]);
        });

        test('creates from YAML array', function (): void {
            $yamlString = "- item1\n- item2\n- item3";
            $yaml = YAML::createFromString($yamlString);

            expect($yaml->decoded)->toBe(['item1', 'item2', 'item3']);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid YAML', function (): void {
            YAML::createFromString('{this is not valid yaml}');
        })->throws(NotYamlException::class);

        test('throws exception for malformed YAML', function (): void {
            YAML::createFromString('invalid: [unclosed');
        })->throws(NotYamlException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles YAML with multiple key-value pairs', function (): void {
            $yamlString = "key1: value1\nkey2: value2\nkey3: value3";
            $yaml = YAML::createFromString($yamlString);

            expect($yaml->decoded)->toBe([
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
            ]);
        });

        test('handles YAML with numeric values', function (): void {
            $yamlString = 'count: 42';
            $yaml = YAML::createFromString($yamlString);

            expect($yaml->decoded)->toBe(['count' => 42]);
        });

        test('handles YAML with boolean values', function (): void {
            $yamlString = 'enabled: true';
            $yaml = YAML::createFromString($yamlString);

            expect($yaml->decoded)->toBe(['enabled' => true]);
        });

        test('handles simple YAML string', function (): void {
            $yamlString = 'simple: value';
            $yaml = YAML::createFromString($yamlString);

            expect($yaml->decoded)->toBe(['simple' => 'value']);
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
