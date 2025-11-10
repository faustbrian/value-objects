<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Enums\CountUnit;

/**
 * Represents a numeric count value with an associated unit of measurement.
 *
 * Immutable value object that encapsulates both the numeric data and its
 * measurement unit, ensuring type safety and preventing invalid state.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class Count
{
    /**
     * Create a new count value object.
     *
     * @param float     $data Numeric value representing the count measurement
     * @param CountUnit $unit Unit of measurement defining how the count value should be interpreted
     */
    private function __construct(
        public float $data,
        public CountUnit $unit,
    ) {}

    /**
     * Create a new count instance from a numeric value and unit.
     *
     * @return self New immutable count instance
     */
    public static function createFrom(float $data, CountUnit $unit): self
    {
        return new self(data: $data, unit: $unit);
    }
}
