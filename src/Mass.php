<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Converters\MassUnitConverter;
use Cline\ValueObjects\Enums\MassUnit;

/**
 * Immutable value object representing a physical mass measurement with units.
 *
 * Encapsulates a mass value together with its unit of measurement, enabling
 * unit conversions and type-safe handling of weight/mass quantities. Supports
 * various mass units through the MassUnit enum and provides conversion
 * capabilities via the MassUnitConverter.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class Mass
{
    /**
     * Create a new mass value object.
     *
     * @param float    $data The numeric value of the mass measurement. Represents
     *                       the magnitude of the weight/mass in the specified unit.
     *                       Can be any positive or negative float value.
     * @param MassUnit $unit The unit of measurement for this mass (e.g., kilograms,
     *                       grams, pounds, ounces). Used for conversions and
     *                       maintaining semantic meaning of the numeric value.
     */
    private function __construct(
        public float $data,
        public MassUnit $unit,
    ) {}

    /**
     * Create a mass value object from a numeric value and unit.
     *
     * Factory method for creating mass instances. The constructor is private
     * to enforce creation through this method, ensuring consistent initialization.
     *
     * @return self The mass value object
     */
    public static function createFrom(float $data, MassUnit $unit): self
    {
        return new self(data: $data, unit: $unit);
    }

    /**
     * Convert this mass to a different unit of measurement.
     *
     * Creates a new Mass instance with the value converted to the target unit.
     * Uses MassUnitConverter to perform accurate unit conversions while
     * maintaining immutability by returning a new instance.
     *
     * @return self A new mass value object with the converted value and unit
     */
    public function convertTo(MassUnit $unit): self
    {
        $convertedData = MassUnitConverter::convert($this->data, $this->unit, $unit);

        $floatData = $convertedData;

        return new self(
            data: $floatData,
            unit: $unit,
        );
    }
}
