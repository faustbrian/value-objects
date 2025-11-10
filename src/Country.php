<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidCountryCodeException;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Represents an ISO 3166-1 compliant country with localized name and codes.
 *
 * Immutable value object that encapsulates country information including
 * the localized country name and both alpha-2 and alpha-3 codes following
 * the ISO 3166-1 standard for country representation.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class Country extends AbstractDataTransferObject
{
    /**
     * Create a new country value object.
     *
     * @param string      $localized Localized human-readable country name in the current locale
     *                               (e.g., "United States" in English, "Estados Unidos" in Spanish)
     * @param string      $alpha2    ISO 3166-1 alpha-2 compliant two-letter country code (e.g., "US", "GB", "DE")
     *                               used as the primary identifier for country identification and comparison
     * @param null|string $alpha3    ISO 3166-1 alpha-3 compliant three-letter country code (e.g., "USA", "GBR", "DEU")
     *                               or null if no alpha-3 code is available for the country
     */
    public function __construct(
        public readonly string $localized,
        public readonly string $alpha2,
        public readonly ?string $alpha3,
    ) {}

    /**
     * Create a country instance from an ISO country code string.
     *
     * Resolves the country code to its localized name and corresponding codes
     * using Symfony's internationalization component. Supports both alpha-2 and
     * alpha-3 country codes as input.
     *
     * @param string $countryCode ISO 3166-1 country code (alpha-2 or alpha-3 format)
     *
     * @throws InvalidCountryCodeException When the country code is invalid or not recognized
     *
     * @return self New immutable country instance
     */
    public static function createFromString(string $countryCode): self
    {
        try {
            return new self(
                Countries::getName($countryCode),
                $countryCode,
                Countries::getAlpha3Code($countryCode),
            );
        } catch (MissingResourceException) {
            throw InvalidCountryCodeException::create($countryCode);
        }
    }

    /**
     * Determine if this country is equal to another country.
     *
     * Comparison is based on the alpha-2 country code, which serves as the
     * unique identifier for each country.
     *
     * @param  self $other Country instance to compare against
     * @return bool True if both countries have the same alpha-2 code
     */
    public function isEqualTo(self $other): bool
    {
        return $this->alpha2 === $other->alpha2;
    }

    /**
     * Convert the country to its string representation.
     *
     * @return string ISO 3166-1 alpha-2 country code
     */
    public function toString(): string
    {
        return $this->alpha2;
    }
}
