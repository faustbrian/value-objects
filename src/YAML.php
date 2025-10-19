<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Serialization\NotYamlException;
use Override;
use Stringable;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml as Symfony;

use function is_array;

/**
 * Immutable YAML value object providing parsing and comparison capabilities.
 *
 * This value object wraps YAML data in both encoded string and decoded array
 * formats. It validates YAML structure on creation using Symfony YAML component
 * and maintains both representations for efficient access. The decoded array
 * provides element access while the encoded string preserves the original format.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final class YAML extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new YAML value object.
     *
     * @param string               $encoded Raw YAML string in valid YAML format. Preserves
     *                                      the original YAML structure including indentation,
     *                                      comments, and formatting for exact reproduction
     *                                      and comparison operations.
     * @param array<string, mixed> $decoded Parsed YAML structure as an associative array.
     *                                      Provides convenient access to YAML elements and
     *                                      their values without requiring repeated parsing
     *                                      operations.
     */
    public function __construct(
        public readonly string $encoded,
        public readonly array $decoded,
    ) {}

    /**
     * Convert the YAML to its string representation.
     *
     * @return string The YAML in its original encoded format
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->encoded;
    }

    /**
     * Create a YAML value object from a string with validation.
     *
     * Parses the YAML string using Symfony YAML component to validate structure
     * and extract elements. The parsing process ensures the YAML is well-formed
     * and creates both encoded and decoded representations for efficient access.
     *
     * @param string $encoded YAML string to parse and validate
     *
     * @throws NotYamlException If the string is not valid YAML or cannot be parsed
     *
     * @return self Validated YAML value object with both string and array forms
     */
    public static function createFromString(string $encoded): self
    {
        try {
            $decoded = Symfony::parse($encoded);
        } catch (ParseException) {
            throw NotYamlException::value($encoded);
        }

        /** @var array<string, mixed> $decodedArray */
        $decodedArray = is_array($decoded) ? $decoded : [];

        return new self(encoded: $encoded, decoded: $decodedArray);
    }

    /**
     * Compare this YAML with another for equality.
     *
     * Performs case-sensitive string comparison of the encoded YAML to determine
     * if two YAML instances represent identical documents. This compares the raw
     * YAML strings rather than semantic equivalence.
     *
     * @param  self $other YAML instance to compare against
     * @return bool True if the YAML strings are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->encoded === $other->encoded;
    }

    /**
     * Get the YAML as a string.
     *
     * @return string The raw YAML in its original encoded format
     */
    public function toString(): string
    {
        return $this->encoded;
    }
}
