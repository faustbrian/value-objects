<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Converters\AreaUnitConverter;
use Cline\ValueObjects\Enums\AreaUnit;

/**
 * Immutable area measurement value object with unit conversion.
 *
 * Represents area measurements in various units (square meters, square feet,
 * acres, etc.) with safe conversion between units. Constructor is private to
 * enforce creation through named constructors that make intent explicit.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class Area
{
    /**
     * Create a new area measurement.
     *
     * Private constructor enforces use of named constructors for clarity.
     *
     * @param float    $data the numeric area value in the specified unit
     * @param AreaUnit $unit the unit of measurement for the area value
     */
    private function __construct(
        public float $data,
        public AreaUnit $unit,
    ) {}

    /**
     * Create an area measurement from a value and unit.
     *
     * @param  float    $data the numeric area value in the specified unit
     * @param  AreaUnit $unit the unit of measurement for the area value
     * @return self     a new immutable area measurement instance
     */
    public static function createFrom(float $data, AreaUnit $unit): self
    {
        return new self(data: $data, unit: $unit);
    }

    /**
     * Convert this area measurement to a different unit.
     *
     * Creates a new instance with the converted value, preserving immutability.
     *
     * @param  AreaUnit $unit the target unit for conversion
     * @return self     a new area measurement instance with converted value and unit
     */
    public function convertTo(AreaUnit $unit): self
    {
        $convertedData = AreaUnitConverter::convert($this->data, $this->unit, $unit);

        return new self(
            data: $convertedData,
            unit: $unit,
        );
    }
}
