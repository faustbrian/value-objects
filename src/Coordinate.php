<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;

use function sprintf;

/**
 * Immutable geographic coordinate value object.
 *
 * Represents a point on Earth's surface using latitude and longitude values.
 * Coordinates are immutable and support equality comparison and string
 * serialization in standard comma-separated format.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class Coordinate extends AbstractDataTransferObject
{
    /**
     * Create a new geographic coordinate.
     *
     * @param Latitude  $latitude  the latitude component of the coordinate (north-south position)
     * @param Longitude $longitude the longitude component of the coordinate (east-west position)
     */
    public function __construct(
        public readonly Latitude $latitude,
        public readonly Longitude $longitude,
    ) {}

    /**
     * Create a coordinate from numeric latitude and longitude values.
     *
     * Convenience factory method that wraps raw floats in value objects.
     *
     * @param  float $latitude  the latitude in decimal degrees (-90 to 90)
     * @param  float $longitude the longitude in decimal degrees (-180 to 180)
     * @return self  a new immutable coordinate instance
     */
    public static function createFromNumber(float $latitude, float $longitude): self
    {
        return new self(
            Latitude::createFromNumber($latitude),
            Longitude::createFromNumber($longitude),
        );
    }

    /**
     * Get the longitude component of this coordinate.
     *
     * @return Longitude the longitude value object
     */
    public function getLongitude(): Longitude
    {
        return $this->longitude;
    }

    /**
     * Compare this coordinate with another for equality.
     *
     * @param  self $other the coordinate to compare against
     * @return bool true if both latitude and longitude match, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->latitude->isEqualTo($other->latitude) && $this->longitude->isEqualTo($other->longitude);
    }

    /**
     * Convert the coordinate to a comma-separated string.
     *
     * @return string the coordinate in "latitude,longitude" format
     */
    public function toString(): string
    {
        return sprintf('%s,%s', $this->latitude->toString(), $this->longitude->toString());
    }
}
