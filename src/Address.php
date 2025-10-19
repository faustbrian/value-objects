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

/**
 * Immutable address value object following xAL standard.
 *
 * Field names are based on the OASIS "eXtensible Address Language" (xAL)
 * standard, a data interchange format for international addresses. The sole
 * exception is the phoneNumber field, added due to its frequent association
 * with addresses for shipping purposes.
 *
 * @see https://en.wikipedia.org/wiki/VCard
 * @see http://www.oasis-open.org/committees/ciq/download.shtml
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class Address extends AbstractDataTransferObject
{
    /**
     * Create a new address value object.
     *
     * Properties are ordered from general to specific, starting with country
     * and ending with person or organization name. This structure follows
     * international addressing standards and supports both residential and
     * commercial addresses worldwide.
     *
     * @param null|CountryCode      $countryCode        CLDR country code including additional regions
     *                                                  for addressing purposes such as Canary Islands (IC).
     *                                                  Null allows for addresses without country specification.
     * @param null|non-empty-string $administrativeArea Broader administrative region within the country.
     *                                                  Known as "state" (US), "region" (France), "province"
     *                                                  (Italy/Canada), "county" (GB), or "prefecture" (Japan).
     * @param string                $locality           City or town name, excluding administrative divisions.
     *                                                  Represents the municipality level in most addressing
     *                                                  systems worldwide.
     * @param null|non-empty-string $dependentLocality  For UK addresses with double-dependent localities,
     *                                                  combines both levels (e.g., "Whaley, Langwith").
     *                                                  Used in certain addressing systems for granular
     *                                                  location specification.
     * @param null|non-empty-string $postalCode         Postal or ZIP code for mail delivery routing.
     *                                                  Format varies by country (e.g., "12345" US,
     *                                                  "SW1A 1AA" UK, "75001" France).
     * @param null|non-empty-string $sortingCode        Additional sorting code used in some countries
     *                                                  for mail distribution (e.g., CEDEX in France).
     *                                                  Rarely used outside specific postal systems.
     * @param null|non-empty-string $addressLine1       Primary street address including number and name,
     *                                                  plus apartment/suite if applicable. Example:
     *                                                  "123 Main Street, Apartment 4".
     * @param null|non-empty-string $addressLine2       Secondary address details such as building identifier,
     *                                                  floor, department, or attention line. Example:
     *                                                  "Building B, Floor 3" or "Attention: Receiving".
     * @param null|non-empty-string $addressLine3       tertiary address information for complex addresses
     *                                                  requiring additional specification beyond standard
     *                                                  two-line format
     * @param null|non-empty-string $fullName           complete name of recipient in single field format,
     *                                                  useful when structured name breakdown is unavailable
     *                                                  or inappropriate for the locale
     * @param null|non-empty-string $givenName          First or given name of recipient following Western
     *                                                  naming conventions. May represent family name in
     *                                                  cultures with different name order.
     * @param null|non-empty-string $additionalName     middle name(s) or additional given names depending
     *                                                  on cultural naming practices and recipient preference
     * @param null|non-empty-string $familyName         Last or family name of recipient following Western
     *                                                  conventions. Position in formatted output varies
     *                                                  by locale and cultural norms.
     * @param null|non-empty-string $organization       Company or organization name when address is for
     *                                                  business rather than residential use. Presence
     *                                                  indicates commercial address.
     * @param null|non-empty-string $locale             Language/region code for formatting preferences
     *                                                  (e.g., "en-US", "fr-FR"). Determines how address
     *                                                  should be displayed or validated.
     * @param null|non-empty-string $phoneNumber        Contact phone number associated with address,
     *                                                  particularly for delivery coordination. Non-standard
     *                                                  xAL field added for practical shipping needs.
     * @param null|float            $latitude           Geographic latitude coordinate for precise location
     *                                                  mapping and distance calculations. Uses decimal
     *                                                  degrees format.
     * @param null|float            $longitude          Geographic longitude coordinate complementing latitude
     *                                                  for complete geospatial positioning. Uses decimal
     *                                                  degrees format.
     */
    public function __construct(
        public readonly ?CountryCode $countryCode,
        public readonly ?string $administrativeArea,
        public readonly string $locality,
        public readonly ?string $dependentLocality,
        public readonly ?string $postalCode,
        public readonly ?string $sortingCode,
        public readonly ?string $addressLine1,
        public readonly ?string $addressLine2,
        public readonly ?string $addressLine3,
        public readonly ?string $fullName,
        public readonly ?string $givenName,
        public readonly ?string $additionalName,
        public readonly ?string $familyName,
        public readonly ?string $organization,
        public readonly ?string $locale,
        public readonly ?string $phoneNumber,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
    ) {}

    /**
     * Determine if this is a residential address.
     *
     * @return bool true when organization is null, indicating a private residence
     */
    public function isPrivateAddress(): bool
    {
        return $this->organization === null;
    }

    /**
     * Determine if this is a commercial address.
     *
     * @return bool true when organization is specified, indicating a business location
     */
    public function isCompanyAddress(): bool
    {
        return $this->organization !== null;
    }

    /**
     * Compare this address with another for equality.
     *
     * @param  self $other the address to compare against
     * @return bool true if all address components match exactly, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return
            $this->countryCode === $other->countryCode
            && $this->administrativeArea === $other->administrativeArea
            && $this->locality === $other->locality
            && $this->dependentLocality === $other->dependentLocality
            && $this->postalCode === $other->postalCode
            && $this->sortingCode === $other->sortingCode
            && $this->addressLine1 === $other->addressLine1
            && $this->addressLine2 === $other->addressLine2
            && $this->addressLine3 === $other->addressLine3
            && $this->fullName === $other->fullName
            && $this->givenName === $other->givenName
            && $this->additionalName === $other->additionalName
            && $this->familyName === $other->familyName
            && $this->organization === $other->organization
            && $this->locale === $other->locale
            && $this->phoneNumber === $other->phoneNumber
            && $this->latitude === $other->latitude
            && $this->longitude === $other->longitude;
    }
}
