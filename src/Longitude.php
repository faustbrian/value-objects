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
 * Immutable value object representing a geographic longitude coordinate.
 *
 * Ensures longitude values are within the valid range of -180 to +180 degrees,
 * where negative values represent western hemisphere and positive values
 * represent eastern hemisphere. Provides type-safe handling of longitude
 * coordinates for geographic calculations and location data.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final class Longitude extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new longitude value object.
     *
     * @param float $value The longitude in decimal degrees. Must be within the range
     *                     of -180 (westernmost meridian) to +180 (easternmost meridian,
     *                     equivalent to -180). Values outside this range are geographically
     *                     invalid and will be rejected during creation via createFromNumber().
     */
    public function __construct(
        public readonly float $value,
    ) {}

    /**
     * Convert the longitude value object to its string representation.
     *
     * @return string The longitude as a decimal string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a longitude value object from a numeric value.
     *
     * Validates that the longitude falls within the valid geographic range
     * of -180 to +180 degrees. Longitudes outside this range are physically
     * impossible on Earth and will cause validation to fail.
     *
     * @param float $value The longitude in decimal degrees
     *
     * @throws OutOfRangeException When the longitude is less than -180 or greater than 180 degrees
     *
     * @return self The validated longitude value object
     */
    public static function createFromNumber(float $value): self
    {
        throw_if($value < -180 || $value > 180, OutOfRangeException::class, 'Longitude must be between -180 and 180');

        return new self($value);
    }

    /**
     * Check if this longitude value object equals another.
     *
     * Equality is determined by strict float comparison. Note that due to
     * floating point precision, very close but not identical values will
     * be considered unequal.
     *
     * @param  self $other The longitude value object to compare against
     * @return bool True if the longitude values are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the raw longitude value as a float.
     *
     * @return float The longitude in decimal degrees
     */
    public function toValue(): float
    {
        return $this->value;
    }

    /**
     * Get the longitude as a string.
     *
     * @return string The longitude in decimal degrees as a string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }
}
