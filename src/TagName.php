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
 * Value object representing a tag name with validation constraints.
 *
 * Enforces business rules for tag names including non-empty content and
 * maximum length restrictions. Used for categorizing and organizing entities
 * within the application with type-safe tag identifiers.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class TagName implements Stringable
{
    /**
     * Create a new tag name value object.
     *
     * @param string $value tag name string that must be non-empty when trimmed
     *                      and cannot exceed 255 characters in length
     *
     * @throws InvalidArgumentException when the value is empty after trimming or exceeds 255 characters
     */
    public function __construct(
        public string $value,
    ) {
        throw_if(in_array(mb_trim($this->value), ['', '0'], true), InvalidArgumentException::class, 'Tag name cannot be empty');

        throw_if(mb_strlen($this->value) > 255, InvalidArgumentException::class, 'Tag name cannot exceed 255 characters');
    }

    /**
     * Convert the tag name to its string representation.
     *
     * @return string The tag name value
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Get the tag name as a string.
     *
     * @return string The tag name value
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Compare this tag name with another for equality.
     *
     * @param  self $other The tag name to compare against
     * @return bool True if both tag names have identical values
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
