<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Database\Eloquent\Casts\StronglyTypedIdCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;

use function in_array;
use function mb_strtolower;
use function sprintf;
use function throw_if;
use function throw_unless;

/**
 * Abstract base class for UUID-based entity identifiers.
 * This class should never be instantiated directly - use specific ID types instead.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
abstract readonly class StronglyTypedId implements Stringable
{
    public function __construct(
        public string $value,
    ) {
        $this->validate($value);
    }

    /**
     * Convert the ID to its string representation.
     *
     * @return string The lowercase UUID string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create an ID instance from a string value.
     *
     * @param string $id The UUID string to create an ID from
     *
     * @throws InvalidArgumentException when the string is not a valid UUID
     *
     * @return static A validated ID instance
     */
    public static function fromString(string $id): static
    {
        /** @phpstan-ignore-next-line new.static -- Factory method pattern where static is guaranteed to be correct subclass */
        return new static(mb_strtolower($id));
    }

    /**
     * Create an ID instance from a UUID interface.
     *
     * @param  UuidInterface $uuid The UUID object to convert
     * @return static        An ID instance containing the lowercase UUID string
     */
    public static function fromUuid(UuidInterface $uuid): static
    {
        /** @phpstan-ignore-next-line new.static -- Factory method pattern where static is guaranteed to be correct subclass */
        return new static(mb_strtolower($uuid->toString()));
    }

    /**
     * Generate a new random ID using UUID v7.
     *
     * UUID v7 provides time-ordered values suitable for database primary keys,
     * combining timestamp-based ordering with random data for uniqueness.
     *
     * @return static A new ID instance with a generated UUID v7
     */
    public static function generate(): static
    {
        /** @phpstan-ignore-next-line new.static -- Factory method pattern where static is guaranteed to be correct subclass */
        return new static(mb_strtolower(Uuid::uuid7()->toString()));
    }

    /**
     * Get the Eloquent cast class string for this ID type.
     *
     * Returns a cast specification that can be used in Eloquent model $casts
     * array to automatically convert database strings to ID instances.
     *
     * @return string The cast specification in "CastClass:IDClass" format
     */
    public static function asEloquentCast(): string
    {
        return StronglyTypedIdCast::class.':'.static::class;
    }

    /**
     * Create an Eloquent attribute accessor/mutator for this ID type.
     *
     * Returns an Attribute instance that handles conversion between database
     * string values and ID instances. Supports null values and accepts both
     * string and ID instances for setting values.
     *
     * @return Attribute Attribute with get/set logic for ID conversion
     */
    public static function asEloquentAttribute(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?static => in_array($value, [null, '', '0'], true) ? null : static::fromString($value),
            set: fn (self|string|null $value): ?string => match (true) {
                $value === null => null,
                $value instanceof self => $value->toString(),
                default => static::fromString($value)->toString(),
            },
        );
    }

    /**
     * Compare this ID with another for equality.
     *
     * Two IDs are equal if they have the same string value and are instances
     * of the same ID class. This prevents accidentally comparing IDs from
     * different entity types.
     *
     * @param  self $other The ID to compare against
     * @return bool True if both IDs are of the same type and have identical values
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value
            && $other instanceof static;
    }

    /**
     * Get the ID as a string.
     *
     * @return string The lowercase UUID string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Validate the ID value format and content.
     *
     * Ensures the value is non-empty and conforms to valid UUID format
     * according to RFC 4122 specifications.
     *
     * @param string $value The ID value to validate
     *
     * @throws InvalidArgumentException when the value is empty or not a valid UUID
     */
    private function validate(string $value): void
    {
        throw_if(
            $value === '' || $value === '0',
            InvalidArgumentException::class,
            sprintf('%s cannot be empty', static::class),
        );

        throw_unless(
            Uuid::isValid($value),
            InvalidArgumentException::class,
            sprintf('Invalid UUID format for %s: %s', static::class, $value),
        );
    }
}
