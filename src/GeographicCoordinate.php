<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;

use function atan2;
use function cos;
use function deg2rad;
use function sin;
use function sprintf;
use function sqrt;

/**
 * Geographic coordinate with factory methods for Google Maps integration.
 *
 * Represents a point on Earth's surface using latitude and longitude coordinates.
 * Includes optional Google Maps metadata (location type and place ID) and provides
 * distance calculation capabilities using the Haversine formula for accurate
 * great-circle distance computation.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class GeographicCoordinate extends AbstractDataTransferObject
{
    /**
     * Create a new geographic coordinate instance.
     *
     * @param Latitude              $latitude     The latitude coordinate representing north-south position.
     *                                            Values range from -90 (South Pole) to +90 (North Pole).
     * @param Longitude             $longitude    The longitude coordinate representing east-west position.
     *                                            Values range from -180 to +180 degrees from the Prime Meridian.
     * @param null|non-empty-string $locationType optional Google Maps location type indicating geocoding
     *                                            precision: "ROOFTOP" (exact), "RANGE_INTERPOLATED" (approximate),
     *                                            "GEOMETRIC_CENTER" (center of area), or "APPROXIMATE" (city-level)
     * @param null|non-empty-string $placeId      optional Google Maps Place ID uniquely identifying a location
     *                                            in the Google Places database, used for reverse geocoding
     *                                            and accessing additional location details
     */
    public function __construct(
        public readonly Latitude $latitude,
        public readonly Longitude $longitude,
        public readonly ?string $locationType = null,
        public readonly ?string $placeId = null,
    ) {}

    /**
     * Create a geographic coordinate from a Google Maps API response.
     *
     * Parses the standard Google Maps Geocoding API response structure to extract
     * latitude, longitude, location type, and place ID. Used when converting
     * addresses to coordinates or processing geocoding results.
     *
     * @param  array<string, mixed> $response google Maps API response array containing
     *                                        'geometry' (with 'location' and 'location_type')
     *                                        and 'place_id' keys from the geocoding response
     * @return self                 a geographic coordinate instance populated with API data
     */
    public static function createFromGoogleMapsResponse(array $response): self
    {
        $geometry = $response['geometry'];

        $location = $geometry['location'];

        /** @var float $lat */
        $lat = $location['lat'];

        /** @var float $lng */
        $lng = $location['lng'];

        /** @var null|string $locationType */
        $locationType = $geometry['location_type'] ?? null;

        /** @var null|string $placeId */
        $placeId = $response['place_id'] ?? null;

        return new self(
            latitude: Latitude::createFromNumber($lat),
            longitude: Longitude::createFromNumber($lng),
            locationType: $locationType,
            placeId: $placeId,
        );
    }

    /**
     * Create a geographic coordinate from raw latitude and longitude values.
     *
     * Simple factory method for creating coordinates from numeric values without
     * additional Google Maps metadata. Use this for user input, database records,
     * or when working with coordinates from non-Google sources.
     *
     * @param  float $lat Latitude value in decimal degrees, range -90 to +90
     * @param  float $lng Longitude value in decimal degrees, range -180 to +180
     * @return self  a geographic coordinate instance without location type or place ID
     */
    public static function createFromCoordinates(float $lat, float $lng): self
    {
        return new self(
            latitude: Latitude::createFromNumber($lat),
            longitude: Longitude::createFromNumber($lng),
        );
    }

    /**
     * Get the latitude coordinate.
     *
     * @return Latitude the latitude coordinate object
     */
    public function getLatitude(): Latitude
    {
        return $this->latitude;
    }

    /**
     * Get the longitude coordinate.
     *
     * @return Longitude the longitude coordinate object
     */
    public function getLongitude(): Longitude
    {
        return $this->longitude;
    }

    /**
     * Get the Google Maps Place ID.
     *
     * @return null|string the place ID if available, null otherwise
     */
    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    /**
     * Get the Google Maps location type.
     *
     * @return null|string the location type indicating geocoding precision, null if not available
     */
    public function getLocationType(): ?string
    {
        return $this->locationType;
    }

    /**
     * Calculate distance to another coordinate using the Haversine formula.
     *
     * Computes the great-circle distance between two points on Earth's surface,
     * accounting for the spherical shape of the planet. This provides accurate
     * distance measurements for most use cases (error < 0.5% for typical distances).
     *
     * ```php
     * $origin = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);  // London
     * $destination = GeographicCoordinate::createFromCoordinates(48.8566, 2.3522);  // Paris
     * $distance = $origin->distanceTo($destination);  // ~343,570 meters
     * ```
     *
     * @param  self  $other The destination coordinate to measure distance to
     * @return float the great-circle distance in meters
     */
    public function distanceTo(self $other): float
    {
        $earthRadius = 6_371_000; // Earth's mean radius in meters

        $latFrom = deg2rad($this->latitude->toValue());
        $lonFrom = deg2rad($this->longitude->toValue());
        $latTo = deg2rad($other->latitude->toValue());
        $lonTo = deg2rad($other->longitude->toValue());

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        // Haversine formula calculation
        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate distance to another coordinate in kilometers.
     *
     * Convenience method that wraps distanceTo() and converts the result to
     * kilometers. Useful for display purposes and distance-based queries.
     *
     * @param  self  $other The destination coordinate to measure distance to
     * @return float the great-circle distance in kilometers
     */
    public function distanceToInKilometers(self $other): float
    {
        return $this->distanceTo($other) / 1_000.0;
    }

    /**
     * Check if this coordinate is within a given radius of a center point.
     *
     * Useful for proximity searches, geofencing, and location-based queries.
     * Uses the Haversine formula for accurate distance calculation.
     *
     * ```php
     * $warehouse = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);
     * $customer = GeographicCoordinate::createFromCoordinates(51.5090, -0.1280);
     * $isNearby = $customer->isWithinRadius($warehouse, 2000);  // true if within 2km
     * ```
     *
     * @param  self  $center         The center point coordinate to measure from
     * @param  float $radiusInMeters The maximum distance in meters to be considered "within"
     * @return bool  true if this coordinate is within the specified radius, false otherwise
     */
    public function isWithinRadius(self $center, float $radiusInMeters): bool
    {
        return $this->distanceTo($center) <= $radiusInMeters;
    }

    /**
     * Compare this coordinate with another for equality.
     *
     * Two coordinates are equal if both their latitude and longitude values match.
     * Google Maps metadata (location type and place ID) is not considered in the
     * equality check, only the geographic position matters.
     *
     * @param  self $other the coordinate to compare against
     * @return bool true if coordinates have identical latitude and longitude values
     */
    public function isEqualTo(self $other): bool
    {
        return $this->latitude->isEqualTo($other->latitude) && $this->longitude->isEqualTo($other->longitude);
    }

    /**
     * Convert the coordinate to a comma-separated string.
     *
     * Returns coordinates in the standard "latitude,longitude" format commonly
     * used by mapping services and APIs. This format is compatible with Google
     * Maps URLs and most geocoding services.
     *
     * @return string the coordinate in "latitude,longitude" format (e.g., "51.5074,-0.1278")
     */
    public function toString(): string
    {
        return sprintf('%s,%s', $this->latitude->toString(), $this->longitude->toString());
    }
}
