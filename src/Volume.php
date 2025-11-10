<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Converters\VolumeUnitConverter;
use Cline\ValueObjects\Enums\VolumeUnit;

/**
 * Immutable volume value object with unit conversion capabilities.
 *
 * This value object represents a volume measurement with an associated unit
 * and provides conversion between different volume units. All conversions
 * create new immutable instances while preserving the original values.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final readonly class Volume
{
    /**
     * Create a new volume value object.
     *
     * @param float      $data Numeric volume measurement value. Can be any positive
     *                         or negative floating-point number representing the
     *                         quantity in the specified unit.
     * @param VolumeUnit $unit Unit of measurement for the volume value. Defines how
     *                         the numeric data should be interpreted and enables
     *                         accurate conversion to other volume units.
     */
    private function __construct(
        public float $data,
        public VolumeUnit $unit,
    ) {}

    /**
     * Create a volume value object from a measurement and unit.
     *
     * @return self New volume value object instance
     */
    public static function createFrom(float $data, VolumeUnit $unit): self
    {
        return new self(data: $data, unit: $unit);
    }

    /**
     * Convert this volume to a different unit of measurement.
     *
     * Creates a new volume instance with the value converted to the target unit
     * using the VolumeUnitConverter. The original instance remains unchanged due
     * to immutability guarantees. The conversion uses precise mathematical ratios
     * between units to maintain accuracy.
     *
     * @return self New volume instance with converted value in the target unit
     */
    public function convertTo(VolumeUnit $unit): self
    {
        $convertedData = VolumeUnitConverter::convert($this->data, $this->unit, $unit);

        $floatData = $convertedData;

        return new self(
            data: $floatData,
            unit: $unit,
        );
    }
}
