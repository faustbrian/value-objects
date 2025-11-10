<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use Cline\ValueObjects\AbstractValueObject;

/**
 * Concrete implementation of AbstractValueObject with nested value objects for testing.
 *
 * @internal
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class NestedTestValueObject extends AbstractValueObject
{
    /**
     * Create a new nested test value object.
     */
    public function __construct(
        public readonly string $id,
        public readonly TestValueObject $nested,
    ) {}

    /**
     * Compare two nested test value objects for equality by value.
     *
     * @param  AbstractValueObject $other the value object to compare against
     * @return bool                true if the value objects are logically equal, false otherwise
     */
    public function equals(AbstractValueObject $other): bool
    {
        // Type safety check - must be same concrete type
        if (!$other instanceof self) {
            return false;
        }

        return $this->id === $other->id
            && $this->nested->equals($other->nested);
    }
}
