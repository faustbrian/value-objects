<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\Exceptions\Serialization\NotXmlException;
use Cline\ValueObjects\XML;

describe('XML', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid XML string', function (): void {
            $xmlString = '<root><foo>bar</foo></root>';
            $xml = XML::createFromString($xmlString);

            expect($xml)->toBeInstanceOf(XML::class);
            expect($xml->encoded)->toBe($xmlString);
        });

        test('creates from XML with attributes', function (): void {
            $xmlString = '<root id="1"><foo attr="value">bar</foo></root>';
            $xml = XML::createFromString($xmlString);

            expect($xml->encoded)->toBe($xmlString);
        });

        test('creates from simple XML', function (): void {
            $xmlString = '<root>content</root>';
            $xml = XML::createFromString($xmlString);

            expect($xml->encoded)->toBe($xmlString);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for unclosed tag', function (): void {
            XML::createFromString('<root><foo>bar</root>');
        })->throws(NotXmlException::class);

        test('throws exception for malformed XML', function (): void {
            XML::createFromString('<root><foo>bar<foo></root>');
        })->throws(NotXmlException::class);

        test('throws exception for invalid XML structure', function (): void {
            XML::createFromString('<<root>>invalid<</root>>');
        })->throws(NotXmlException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles nested XML elements', function (): void {
            $xmlString = '<root><parent><child>value</child></parent></root>';
            $xml = XML::createFromString($xmlString);

            expect($xml->encoded)->toBe($xmlString);
        });

        test('handles XML with namespaces', function (): void {
            $xmlString = '<ns:root xmlns:ns="http://example.com"><ns:foo>bar</ns:foo></ns:root>';
            $xml = XML::createFromString($xmlString);

            expect($xml->encoded)->toContain('ns:root');
        });

        test('handles self-closing tags', function (): void {
            $xmlString = '<root><foo/></root>';
            $xml = XML::createFromString($xmlString);

            expect($xml->encoded)->toContain('root');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
