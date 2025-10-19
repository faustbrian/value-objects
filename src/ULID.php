<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use InvalidArgumentException;
use Override;
use Stringable;
use Symfony\Component\Uid\Ulid as Symfony;

/**
 * Value object representing a Universally Unique Lexicographically Sortable Identifier (ULID).
 *
 * ULID provides 128-bit identifiers that are lexicographically sortable, URL-safe,
 * and encode timestamp information. ULIDs are case-insensitive and more compact
 * than UUIDs while maintaining similar uniqueness guarantees.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class ULID extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new ULID value object.
     *
     * @param string $value ULID string in Crockford's base32 encoding (26 characters)
     */
    public function __construct(
        public readonly string $value,
    ) {}

    /**
     * Convert the ULID to its string representation.
     *
     * @return string The ULID value as a string
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a ULID instance from a string value.
     *
     * Validates the provided string is a valid ULID format using Symfony's
     * ULID component. ULIDs must be 26 characters in Crockford's base32
     * encoding and encode both timestamp and randomness components.
     *
     * @param string $value The ULID string to validate
     *
     * @throws InvalidArgumentException when the value is not a valid ULID format
     *
     * @return self A validated ULID instance
     */
    public static function createFromString(string $value): self
    {
        try {
            Symfony::fromString($value);

            return new self($value);
        } catch (InvalidArgumentException $invalidArgumentException) {
            throw new InvalidArgumentException('Invalid ULID: '.$value, 0, $invalidArgumentException);
        }
    }

    /**
     * Compare this ULID with another for equality.
     *
     * @param  self $other The ULID to compare against
     * @return bool True if both ULIDs have identical values
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the ULID as a string.
     *
     * @return string The ULID value as a string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
