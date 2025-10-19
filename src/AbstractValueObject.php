<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractData;

/**
 * Base class for immutable value objects.
 *
 * Extends our high-performance DTO base for consistency; can be decoupled later
 * by introducing its own internals without changing dependents. All value objects
 * are immutable by design and compared by value rather than identity.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
abstract class AbstractValueObject extends AbstractData
{
    /**
     * Compare two value objects for equality by value.
     *
     * Implementations must compare all relevant properties to determine if two
     * value objects represent the same logical value. Identity comparison is
     * not sufficient for value objects.
     *
     * @param  self $other the value object to compare against
     * @return bool true if the value objects are logically equal, false otherwise
     */
    abstract public function equals(self $other): bool;
}
