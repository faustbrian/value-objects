<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Serialization\NotJsonException;
use JsonException;
use Override;
use Stringable;

use const JSON_THROW_ON_ERROR;

use function json_decode;

/**
 * Immutable value object representing JSON data in both encoded and decoded forms.
 *
 * This value object stores JSON data in both its encoded string form and
 * its decoded array representation, ensuring consistent access to both
 * representations without repeated serialization/deserialization overhead.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final class JSON extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new JSON value object.
     *
     * @param string               $encoded The JSON-encoded string representation. Must be valid JSON
     *                                      that can be successfully parsed by json_decode. Used for
     *                                      storage and transmission of the data in its serialized form.
     * @param array<string, mixed> $decoded The decoded array representation of the JSON data. Contains
     *                                      the parsed structure for direct access to the data without
     *                                      requiring repeated decoding operations.
     */
    public function __construct(
        public readonly string $encoded,
        public readonly array $decoded,
    ) {}

    /**
     * Convert the JSON value object to its string representation.
     *
     * @return string The JSON-encoded string
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->encoded;
    }

    /**
     * Create a JSON value object from a JSON-encoded string.
     *
     * Validates that the input is valid JSON before creating the value object.
     * Decodes the JSON to verify structure integrity and stores both the
     * encoded and decoded representations for efficient access.
     *
     * @param string $encoded The JSON-encoded string to parse and validate
     *
     * @throws NotJsonException When the input string is not valid JSON or cannot be decoded
     *
     * @return self The JSON value object with both encoded and decoded forms
     */
    public static function createFromString(string $encoded): self
    {
        try {
            $decoded = self::decode($encoded);
        } catch (JsonException) {
            throw NotJsonException::value($encoded);
        }

        return new self($encoded, $decoded);
    }

    /**
     * Check if this JSON value object equals another.
     *
     * Equality is determined by comparing the encoded string representations.
     * This ensures that JSON with identical content but different formatting
     * (whitespace, key order) is treated as different values.
     *
     * @param  self $other The JSON value object to compare against
     * @return bool True if the encoded strings are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->encoded === $other->encoded;
    }

    /**
     * Get the JSON-encoded string representation.
     *
     * @return string The JSON-encoded string
     */
    public function toString(): string
    {
        return $this->encoded;
    }

    /**
     * Decode a JSON string to an associative array.
     *
     * Uses json_decode with JSON_THROW_ON_ERROR flag to ensure exceptions
     * are thrown for invalid JSON instead of returning null. Limits depth
     * to 512 levels to prevent deeply nested structures from causing issues.
     *
     * @param string $encoded The JSON string to decode
     *
     * @throws JsonException When JSON decoding fails due to invalid syntax or structure
     *
     * @return array<string, mixed> The decoded associative array
     */
    private static function decode(string $encoded): array
    {
        return json_decode(
            $encoded,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
    }
}
