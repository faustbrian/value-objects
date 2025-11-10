<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Serialization\NotXmlException;
use Saloon\XmlWrangler\XmlReader;
use Throwable;

/**
 * Immutable XML value object providing parsing and comparison capabilities.
 *
 * This value object wraps XML data in both encoded string and decoded array
 * formats. It validates XML structure on creation and maintains both representations
 * for efficient access to either form. The decoded array provides element access
 * while the encoded string preserves the original XML structure.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final class XML extends AbstractDataTransferObject
{
    /**
     * Create a new XML value object.
     *
     * @param string               $encoded Raw XML string in valid XML format. Preserves
     *                                      the original XML structure including whitespace,
     *                                      comments, and processing instructions for exact
     *                                      reproduction and comparison operations.
     * @param array<string, mixed> $decoded Parsed XML elements as an associative array
     *                                      structure. Provides convenient access to XML
     *                                      elements and their values without requiring
     *                                      repeated parsing operations.
     */
    public function __construct(
        public readonly string $encoded,
        public readonly array $decoded,
    ) {}

    /**
     * Create an XML value object from a string with validation.
     *
     * Parses the XML string using Saloon XmlReader to validate structure and
     * extract elements. The parsing process ensures the XML is well-formed and
     * creates both encoded and decoded representations for efficient access.
     *
     * @param string $encoded XML string to parse and validate
     *
     * @throws NotXmlException If the string is not valid XML or cannot be parsed
     *
     * @return self Validated XML value object with both string and array forms
     */
    public static function createFromString(string $encoded): self
    {
        try {
            $decoded = XmlReader::fromString($encoded)->elements();
        } catch (Throwable) {
            throw NotXmlException::value($encoded);
        }

        return new self(encoded: $encoded, decoded: $decoded);
    }

    /**
     * Compare this XML with another for equality.
     *
     * Performs case-sensitive string comparison of the encoded XML to determine
     * if two XML instances represent identical documents. This compares the raw
     * XML strings rather than semantic equivalence.
     *
     * @param  self $other XML instance to compare against
     * @return bool True if the XML strings are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->encoded === $other->encoded;
    }

    /**
     * Get the XML as a string.
     *
     * @return string The raw XML in its original encoded format
     */
    public function toString(): string
    {
        return $this->encoded;
    }
}
