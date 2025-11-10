<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use OutOfRangeException;
use Stringable;

use function throw_if;

/**
 * Immutable value object representing a geographic latitude coordinate.
 *
 * Ensures latitude values are within the valid range of -90 to +90 degrees,
 * where negative values represent southern hemisphere and positive values
 * represent northern hemisphere. Provides type-safe handling of latitude
 * coordinates for geographic calculations and location data.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final class Latitude extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new latitude value object.
     *
     * @param float $value The latitude in decimal degrees. Must be within the range
     *                     of -90 (South Pole) to +90 (North Pole). Values outside this
     *                     range are geographically invalid and will be rejected during
     *                     creation via createFromNumber().
     */
    public function __construct(
        public readonly float $value,
    ) {}

    /**
     * Convert the latitude value object to its string representation.
     *
     * @return string The latitude as a decimal string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a latitude value object from a numeric value.
     *
     * Validates that the latitude falls within the valid geographic range
     * of -90 to +90 degrees. Latitudes outside this range are physically
     * impossible on Earth and will cause validation to fail.
     *
     * @param float $value The latitude in decimal degrees
     *
     * @throws OutOfRangeException When the latitude is less than -90 or greater than 90 degrees
     *
     * @return self The validated latitude value object
     */
    public static function createFromNumber(float $value): self
    {
        throw_if($value < -90 || $value > 90, OutOfRangeException::class, 'Latitude must be between -90 and 90');

        return new self($value);
    }

    /**
     * Check if this latitude value object equals another.
     *
     * Equality is determined by strict float comparison. Note that due to
     * floating point precision, very close but not identical values will
     * be considered unequal.
     *
     * @param  self $other The latitude value object to compare against
     * @return bool True if the latitude values are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the raw latitude value as a float.
     *
     * @return float The latitude in decimal degrees
     */
    public function toValue(): float
    {
        return $this->value;
    }

    /**
     * Get the latitude as a string.
     *
     * @return string The latitude in decimal degrees as a string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }
}
