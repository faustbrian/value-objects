<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Converters\LengthUnitConverter;
use Cline\ValueObjects\Enums\LengthUnit;

/**
 * Immutable value object representing a physical length measurement with units.
 *
 * Encapsulates a length value together with its unit of measurement, enabling
 * unit conversions and type-safe handling of physical distances. Supports various
 * length units through the LengthUnit enum and provides conversion capabilities
 * via the LengthUnitConverter.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class Length
{
    /**
     * Create a new length value object.
     *
     * @param float      $data The numeric value of the length measurement. Represents
     *                         the magnitude of the distance in the specified unit.
     *                         Can be any positive or negative float value.
     * @param LengthUnit $unit The unit of measurement for this length (e.g., meters,
     *                         kilometers, miles, feet). Used for conversions and
     *                         maintaining semantic meaning of the numeric value.
     */
    private function __construct(
        public float $data,
        public LengthUnit $unit,
    ) {}

    /**
     * Create a length value object from a numeric value and unit.
     *
     * Factory method for creating length instances. The constructor is private
     * to enforce creation through this method, ensuring consistent initialization.
     *
     * @return self The length value object
     */
    public static function createFrom(float $data, LengthUnit $unit): self
    {
        return new self(data: $data, unit: $unit);
    }

    /**
     * Convert this length to a different unit of measurement.
     *
     * Creates a new Length instance with the value converted to the target unit.
     * Uses LengthUnitConverter to perform accurate unit conversions while
     * maintaining immutability by returning a new instance.
     *
     * @return self A new length value object with the converted value and unit
     */
    public function convertTo(LengthUnit $unit): self
    {
        $convertedData = LengthUnitConverter::convert($this->data, $this->unit, $unit);

        $floatData = $convertedData;

        return new self(
            data: $floatData,
            unit: $unit,
        );
    }
}
