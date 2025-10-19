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
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid as Ramsey;
use Stringable;

/**
 * Immutable UUID value object providing RFC 4122 UUID validation and comparison.
 *
 * This value object wraps a UUID string and provides validation using the Ramsey
 * UUID library to ensure conformance to RFC 4122 standards. It supports UUID
 * comparison and string conversion operations while maintaining immutability.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final class UUID extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new UUID value object.
     *
     * @param string $value RFC 4122 compliant UUID string in canonical format
     *                      (e.g., "550e8400-e29b-41d4-a716-446655440000"). The value
     *                      must be a valid UUID string format with lowercase hexadecimal
     *                      digits and hyphens in the standard 8-4-4-4-12 pattern.
     */
    public function __construct(
        public readonly string $value,
    ) {}

    /**
     * Convert the UUID to its string representation.
     *
     * @return string The UUID in canonical string format
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a UUID value object from a string with validation.
     *
     * Validates the provided string against RFC 4122 UUID format requirements
     * using Ramsey UUID library. The string must conform to the standard UUID
     * format with proper hexadecimal digits and hyphen placement.
     *
     * @param string $value UUID string to validate and wrap
     *
     * @throws InvalidArgumentException If the string is not a valid RFC 4122 UUID format
     *
     * @return self Validated UUID value object
     */
    public static function createFromString(string $value): self
    {
        try {
            Ramsey::fromString($value);

            return new self($value);
        } catch (InvalidUuidStringException $invalidUuidStringException) {
            throw new InvalidArgumentException('Invalid UUID: '.$value, 0, $invalidUuidStringException);
        }
    }

    /**
     * Compare this UUID with another for equality.
     *
     * Performs case-sensitive string comparison of the UUID values to determine
     * if two UUID instances represent the same identifier.
     *
     * @param  self $other UUID instance to compare against
     * @return bool True if the UUIDs are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the UUID as a string.
     *
     * @return string The UUID in canonical string format
     */
    public function toString(): string
    {
        return $this->value;
    }
}
