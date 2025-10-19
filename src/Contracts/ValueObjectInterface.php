<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Contracts;

use Stringable;

/**
 * Interface for value objects that are immutable and comparable.
 *
 * Defines the core contract for value objects in the application. Value objects
 * are compared by value rather than identity and must be immutable. All
 * implementations must provide string conversion and equality comparison.
 *
 * @author Brian Faust <brian@cline.sh>
 */
interface ValueObjectInterface extends Stringable
{
    /**
     * Compare this value object with another for equality.
     *
     * Implementations must perform value-based comparison, checking that all
     * relevant properties match. Identity comparison is not sufficient for
     * value objects as two distinct instances with identical values should
     * be considered equal.
     *
     * @param  self $other the value object to compare against
     * @return bool true if the value objects are logically equal, false otherwise
     */
    public function isEqualTo(self $other): bool;

    /**
     * Convert the value object to its string representation.
     *
     * Returns a meaningful string representation of the value object suitable
     * for display, logging, or serialization. The format should be consistent
     * and human-readable.
     *
     * @return string the string representation of this value object
     */
    public function toString(): string;
}
