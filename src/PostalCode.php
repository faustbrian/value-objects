<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Brick\Postcode\InvalidPostcodeException;
use Brick\Postcode\PostcodeFormatter;
use Brick\Postcode\UnknownCountryException;
use Cline\Data\Core\AbstractDataTransferObject;
use Cline\Intl\Enum\CountryCode;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidPostalCodeException;
use Override;
use Stringable;

/**
 * Value object representing a validated postal code for a specific country.
 *
 * Wraps Brick\Postcode library to validate and format postal codes according
 * to country-specific rules and patterns. Ensures postal codes conform to
 * regional standards including formatting, length, and character requirements.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final class PostalCode extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new postal code value object.
     *
     * @param string      $value       validated and formatted postal code string conforming
     *                                 to the country's postal code format and rules
     * @param CountryCode $countryCode ISO 3166-1 alpha-2 country code enum identifying the country
     *                                 whose postal code format applies to this value
     */
    public function __construct(
        public readonly string $value,
        public readonly CountryCode $countryCode,
    ) {}

    /**
     * Convert the postal code to its string representation.
     *
     * @return string The formatted postal code
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a postal code instance from string values.
     *
     * @param string $code        The postal code to validate and format
     * @param string $countryCode ISO 3166-1 alpha-2 country code string (e.g., "US", "SE")
     *
     * @throws InvalidPostalCodeException when the postal code is invalid for the specified country
     *
     * @return self A validated postal code instance
     */
    public static function createFromString(string $code, string $countryCode): self
    {
        return self::createFromEnum($code, CountryCode::from($countryCode));
    }

    /**
     * Create a postal code instance with validation and formatting.
     *
     * Validates the postal code against country-specific rules and applies
     * standard formatting. The Brick\Postcode library handles validation and
     * formatting according to each country's postal system requirements.
     *
     * @param string      $code    The postal code to validate and format
     * @param CountryCode $country Country code enum defining validation and formatting rules
     *
     * @throws InvalidPostalCodeException when the postal code is invalid for the specified country
     *
     * @return self A validated and formatted postal code instance
     */
    public static function createFromEnum(string $code, CountryCode $country): self
    {
        try {
            $formatted = new PostcodeFormatter()->format($country->value, $code);

            return new self(value: $formatted, countryCode: $country);
        } catch (InvalidPostcodeException|UnknownCountryException) {
            throw InvalidPostalCodeException::create($code, $country->value);
        }
    }

    /**
     * Create a postal code from Google Maps API address component.
     *
     * @param array{short_name: string} $component Google Maps address component containing postal code
     * @param CountryCode               $country   Country code for validation context
     *
     * @throws InvalidPostalCodeException when the postal code is invalid for the specified country
     *
     * @return self A validated postal code instance
     */
    public static function createFromGoogleMapsComponent(array $component, CountryCode $country): self
    {
        return self::createFromEnum($component['short_name'], $country);
    }

    /**
     * Get the postal code as a string.
     *
     * @return string The formatted postal code
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get the country code associated with this postal code.
     *
     * @return CountryCode The country code enum
     */
    public function getCountryCode(): CountryCode
    {
        return $this->countryCode;
    }
}
