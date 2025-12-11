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
 * Concrete implementation of AbstractValueObject for testing.
 *
 * @author Brian Faust <brian@cline.sh>
 * @internal
 */
final class TestValueObject extends AbstractValueObject
{
    /**
     * Create a new test value object.
     */
    public function __construct(
        public readonly string $name,
        public readonly int $value,
        public readonly ?string $optional = null,
    ) {}

    /**
     * Compare two test value objects for equality by value.
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

        return $this->name === $other->name
            && $this->value === $other->value
            && $this->optional === $other->optional;
    }
}
