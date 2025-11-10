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

use function mb_trim;
use function sprintf;
use function throw_if;

/**
 * Base class for string-backed value objects.
 *
 * Provides shared validation and equality logic for identifiers that were
 * previously sourced from the shared kernel. Ensures all string values are
 * trimmed and non-empty, preventing invalid state from being created.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.0
 *
 * @psalm-immutable
 */
abstract class AbstractStringValueObject extends AbstractValueObject implements Stringable
{
    /**
     * The normalized string value.
     */
    protected readonly string $value;

    /**
     * Create a new string value object.
     *
     * @param string $value the string value to wrap, will be trimmed and validated as non-empty
     *
     * @throws InvalidArgumentException when the trimmed value is empty
     */
    public function __construct(string $value)
    {
        $normalized = mb_trim($value);

        throw_if($normalized === '', InvalidArgumentException::class, sprintf('%s cannot be empty', static::class));

        $this->value = $normalized;
    }

    /**
     * Convert the value object to its string representation.
     *
     * @return string the normalized string value
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Get the underlying string value.
     *
     * @return string the normalized string value
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare this value object with another for equality.
     *
     * Two string value objects are equal if they are instances of the exact
     * same class and contain identical string values.
     *
     * @param  AbstractValueObject $other the value object to compare against
     * @return bool                true if both class and value match, false otherwise
     */
    public function equals(AbstractValueObject $other): bool
    {
        if (static::class !== $other::class) {
            return false;
        }

        /** @var self $other */
        return $this->value === $other->value;
    }
}
