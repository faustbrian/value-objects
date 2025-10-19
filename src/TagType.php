<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use InvalidArgumentException;
use Stringable;

use function in_array;
use function mb_strlen;
use function mb_trim;
use function throw_if;

/**
 * Value object representing an optional tag type classification.
 *
 * Provides hierarchical categorization for tags by defining a type or category.
 * Supports nullable values to represent default or untyped tags. When provided,
 * enforces validation constraints for non-empty content and maximum length.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class TagType implements Stringable
{
    /**
     * Create a new tag type value object.
     *
     * @param null|string $value Optional tag type string that, when provided, must be
     *                           non-empty when trimmed and cannot exceed 100 characters.
     *                           A null value represents the default type for untyped tags.
     *
     * @throws InvalidArgumentException when the value is an empty string after trimming or exceeds 100 characters
     */
    public function __construct(
        public ?string $value = null,
    ) {
        if ($this->value === null) {
            return;
        }

        throw_if(in_array(mb_trim($this->value), ['', '0'], true), InvalidArgumentException::class, 'Tag type cannot be empty string');

        throw_if(mb_strlen($this->value) > 100, InvalidArgumentException::class, 'Tag type cannot exceed 100 characters');
    }

    /**
     * Convert the tag type to its string representation.
     *
     * @return string The tag type value, or empty string if null
     */
    public function __toString(): string
    {
        return $this->value ?? '';
    }

    /**
     * Get the tag type as a string.
     *
     * @return null|string The tag type value, or null for default type
     */
    public function toString(): ?string
    {
        return $this->value;
    }

    /**
     * Compare this tag type with another for equality.
     *
     * @param  self $other The tag type to compare against
     * @return bool True if both tag types have identical values (including both being null)
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Check if this is the default (untyped) tag type.
     *
     * @return bool True if the tag type value is null
     */
    public function isDefault(): bool
    {
        return $this->value === null;
    }
}
