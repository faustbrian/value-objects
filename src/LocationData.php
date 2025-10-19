<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\Intl\Enum\CountryCode;
use InvalidArgumentException;

use function array_key_exists;
use function in_array;
use function is_array;
use function mb_strtoupper;
use function reset;
use function str_starts_with;

/**
 * Immutable value object representing structured location data from Google Maps API.
 *
 * Encapsulates geographic and administrative location information extracted from
 * Google Maps geocoding responses. Provides structured access to coordinates,
 * country codes, postal codes, locality information, and administrative divisions
 * while handling various response formats and edge cases.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.4
 *
 * @psalm-immutable
 */
final class LocationData extends AbstractDataTransferObject
{
    /**
     * Create a new location data value object.
     *
     * @param GeographicCoordinate                           $coordinate           The geographic coordinates (latitude/longitude)
     *                                                                             representing the precise location on Earth's surface.
     *                                                                             Used for mapping, distance calculations, and spatial queries.
     * @param CountryCode                                    $countryCode          The ISO 3166-1 alpha-2 country code identifying the country
     *                                                                             where this location is situated. Provides standardized
     *                                                                             country identification for validation and localization.
     * @param PostalCode                                     $postalCode           The postal/ZIP code for this location. May be empty for
     *                                                                             locations without postal codes (e.g., rural areas, some
     *                                                                             international locations). Used for address validation.
     * @param string                                         $locality             The city, town, or locality name. Extracted from Google Maps
     *                                                                             with multiple fallback strategies to ensure a value is always
     *                                                                             available. Used as the primary human-readable location identifier.
     * @param null|non-empty-string                          $streetAddress        The street address component including street number and name.
     *                                                                             May be null for locations without specific street addresses
     *                                                                             (e.g., landmarks, geographic features, broad areas).
     * @param null|array<non-empty-string, non-empty-string> $administrativeLevels Administrative division hierarchy mapping keys like
     *                                                                             'administrative_area_level_1' to their localized names.
     *                                                                             Represents states, provinces, regions, and other
     *                                                                             administrative subdivisions from Google Maps data.
     * @param null|non-empty-string                          $formattedAddress     The complete formatted address string from Google Maps.
     *                                                                             Human-readable representation combining all address components
     *                                                                             in the format appropriate for the location's country.
     */
    public function __construct(
        public readonly GeographicCoordinate $coordinate,
        public readonly CountryCode $countryCode,
        public readonly PostalCode $postalCode,
        public readonly string $locality,
        public readonly ?string $streetAddress,
        public readonly ?array $administrativeLevels,
        public readonly ?string $formattedAddress,
    ) {}

    /**
     * Create a location data value object from a Google Maps API response.
     *
     * Parses a Google Maps geocoding API result array and extracts all relevant
     * location components. Handles various response formats and applies fallback
     * strategies to ensure reliable data extraction even with incomplete responses.
     *
     * @param array $result The Google Maps geocoding API response array containing
     *                      address components, geometry, and formatted address data
     *
     * @throws InvalidArgumentException When required location components cannot be extracted
     *
     * @return self The parsed and structured location data
     */
    public static function createFromGoogleMapsResult(array $result): self
    {
        $countryCode = self::extractCountryCode($result);

        return new self(
            coordinate: GeographicCoordinate::createFromGoogleMapsResponse($result),
            countryCode: $countryCode,
            postalCode: self::extractPostalCode($result, $countryCode),
            locality: self::extractLocality($result),
            streetAddress: $result['street_address'] ?? null,
            administrativeLevels: self::extractAdministrativeLevels($result),
            formattedAddress: $result['formatted_address'] ?? null,
        );
    }

    /**
     * Get the geographic coordinate.
     *
     * @return GeographicCoordinate The latitude and longitude coordinates
     */
    public function getCoordinate(): GeographicCoordinate
    {
        return $this->coordinate;
    }

    /**
     * Get the country code.
     *
     * @return CountryCode The ISO 3166-1 alpha-2 country code
     */
    public function getCountryCode(): CountryCode
    {
        return $this->countryCode;
    }

    /**
     * Get the postal code.
     *
     * @return PostalCode The postal/ZIP code for this location
     */
    public function getPostalCode(): PostalCode
    {
        return $this->postalCode;
    }

    /**
     * Get the locality name.
     *
     * @return non-empty-string The city, town, or locality name
     */
    public function getLocality(): string
    {
        return $this->locality;
    }

    /**
     * Get the street address.
     *
     * @return null|non-empty-string The street address or null if unavailable
     */
    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    /**
     * Get the formatted address.
     *
     * @return null|non-empty-string The complete formatted address or null if unavailable
     */
    public function getFormattedAddress(): ?string
    {
        return $this->formattedAddress;
    }

    /**
     * Get the administrative levels hierarchy.
     *
     * @return null|array<non-empty-string, non-empty-string> Array mapping administrative level keys to names, or null if unavailable
     */
    public function getAdministrativeLevels(): ?array
    {
        return $this->administrativeLevels;
    }

    /**
     * Extract the country code from a Google Maps result.
     *
     * Attempts to find the country code first in the direct result fields,
     * then searches through address components. The country code is normalized
     * to uppercase to match ISO 3166-1 alpha-2 format.
     *
     * @param array $result The Google Maps API response array
     *
     * @throws InvalidArgumentException When no country code can be found in the result
     *
     * @return CountryCode The extracted and validated country code
     */
    private static function extractCountryCode(array $result): CountryCode
    {
        if (array_key_exists('country_code', $result)) {
            return CountryCode::from(mb_strtoupper((string) $result['country_code']));
        }

        foreach ($result['address_components'] ?? [] as $component) {
            if (in_array('country', $component['types'], true)) {
                return CountryCode::from(mb_strtoupper((string) $component['short_name']));
            }
        }

        throw new InvalidArgumentException('Country code not found in Google Maps result');
    }

    /**
     * Extract the postal code from a Google Maps result.
     *
     * Searches for postal code in direct result fields and address components.
     * Returns an empty postal code for locations that don't have postal codes
     * (e.g., some rural areas, geographic features) rather than failing.
     *
     * @param  array       $result      The Google Maps API response array
     * @param  CountryCode $countryCode The country code for postal code validation
     * @return PostalCode  The extracted postal code or empty postal code if not found
     */
    private static function extractPostalCode(array $result, CountryCode $countryCode): PostalCode
    {
        if (array_key_exists('postal_code', $result)) {
            return PostalCode::createFromString(code: $result['postal_code'], countryCode: $countryCode->value);
        }

        foreach ($result['address_components'] ?? [] as $component) {
            if (in_array('postal_code', $component['types'], true)) {
                return PostalCode::createFromGoogleMapsComponent(component: $component, country: $countryCode);
            }
        }

        return PostalCode::createFromString(code: '', countryCode: $countryCode->value);
    }

    /**
     * Extract the locality name from a Google Maps result with cascading fallbacks.
     *
     * Implements a comprehensive fallback strategy to extract a locality name:
     * 1. Direct locality field or address component
     * 2. Postal town (common in UK addresses)
     * 3. Sublocality level 1 (neighborhoods, districts)
     * 4. Administrative area level 1 (states, provinces)
     * 5. Postal code as last resort
     *
     * This ensures a locality value is always available even for locations
     * with incomplete or non-standard Google Maps data.
     *
     * @param array $result The Google Maps API response array
     *
     * @throws InvalidArgumentException When no locality can be determined from any fallback
     *
     * @return non-empty-string The extracted locality name
     */
    private static function extractLocality(array $result): string
    {
        if (array_key_exists('locality', $result)) {
            if (is_array($result['locality'])) {
                return reset($result['locality']);
            }

            return $result['locality'];
        }

        foreach ($result['address_components'] ?? [] as $component) {
            if (in_array('locality', $component['types'], true)) {
                return $component['long_name'];
            }
        }

        if (array_key_exists('postal_town', $result)) {
            if (is_array($result['postal_town'])) {
                return reset($result['postal_town']);
            }

            return $result['postal_town'];
        }

        if (array_key_exists('sublocality_level_1', $result)) {
            if (is_array($result['sublocality_level_1'])) {
                return reset($result['sublocality_level_1']);
            }

            return $result['sublocality_level_1'];
        }

        if (array_key_exists('administrative_area_level_1', $result)) {
            if (is_array($result['administrative_area_level_1'])) {
                return reset($result['administrative_area_level_1']);
            }

            return $result['administrative_area_level_1'];
        }

        if (array_key_exists('postal_code', $result)) {
            if (is_array($result['postal_code'])) {
                return reset($result['postal_code']);
            }

            return $result['postal_code'];
        }

        throw new InvalidArgumentException('Locality not found in Google Maps result');
    }

    /**
     * Extract administrative levels hierarchy from a Google Maps result.
     *
     * Collects all administrative division levels (states, provinces, regions,
     * districts, etc.) from both direct result fields and address components.
     * Google Maps can provide up to 7 levels of administrative divisions
     * depending on the country and location specificity.
     *
     * @param  array                                     $result The Google Maps API response array
     * @return array<non-empty-string, non-empty-string> Mapping of administrative level keys to their localized names
     */
    private static function extractAdministrativeLevels(array $result): array
    {
        $levels = [];

        for ($i = 1; $i <= 7; ++$i) {
            $key = 'administrative_area_level_'.$i;

            if (!array_key_exists($key, $result)) {
                continue;
            }

            $levels[$key] = $result[$key];
        }

        foreach ($result['address_components'] ?? [] as $component) {
            foreach ($component['types'] as $type) {
                if (!str_starts_with((string) $type, 'administrative_area_level_')) {
                    continue;
                }

                $levels[$type] = $component['long_name'];
            }
        }

        return $levels;
    }
}
