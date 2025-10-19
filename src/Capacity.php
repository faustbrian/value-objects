<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Converters\CapacityUnitConverter;
use Cline\ValueObjects\Enums\CapacityUnit;

/**
 * Immutable volume/capacity measurement value object with unit conversion.
 *
 * Represents volume or capacity measurements in various units (liters, gallons,
 * cubic meters, etc.) with safe conversion between units. Constructor is private
 * to enforce creation through named constructors that make intent explicit.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class Capacity
{
    /**
     * Create a new capacity measurement.
     *
     * Private constructor enforces use of named constructors for clarity.
     *
     * @param float        $data the numeric capacity value in the specified unit
     * @param CapacityUnit $unit the unit of measurement for the capacity value
     */
    private function __construct(
        public float $data,
        /**
         * @phpstan-ignore-next-line class.notFound -- CapacityUnit enum provided by consuming application
         */
        public CapacityUnit $unit,
    ) {}

    /**
     * Create a capacity measurement from a value and unit.
     *
     * @return self a new immutable capacity measurement instance
     */
    /**
     * @phpstan-ignore-next-line class.notFound -- CapacityUnit enum provided by consuming application
     */
    public static function createFrom(float $data, CapacityUnit $unit): self
    {
        return new self(data: $data, unit: $unit);
    }

    /**
     * Convert this capacity measurement to a different unit.
     *
     * Creates a new instance with the converted value, preserving immutability.
     *
     * @return self a new capacity measurement instance with converted value and unit
     */
    /**
     * @phpstan-ignore-next-line class.notFound -- CapacityUnit enum provided by consuming application
     */
    public function convertTo(CapacityUnit $unit): self
    {
        /** @phpstan-ignore-next-line class.notFound -- CapacityUnitConverter provided by consuming application */
        $convertedData = CapacityUnitConverter::convert($this->data, $this->unit, $unit);

        /** @phpstan-ignore-next-line cast.double -- Result from external converter is guaranteed to be numeric */
        $floatData = (float) $convertedData;

        return new self(
            data: $floatData,
            unit: $unit,
        );
    }
}
