<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Brick\PhoneNumber\PhoneNumber as Brick;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;
use Cline\Data\Core\AbstractDataTransferObject;
use JsonSerializable;
use Override;
use Stringable;

/**
 * Value object representing an international phone number.
 *
 * Wraps Brick\PhoneNumber library to provide immutable phone number handling
 * with validation, formatting, and metadata extraction capabilities. Supports
 * international dialing formats and region-specific number validation.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @see https://github.com/brick/phonenumber
 *
 * @psalm-immutable
 */
final class PhoneNumber extends AbstractDataTransferObject implements JsonSerializable, Stringable
{
    /**
     * Create a new phone number value object.
     *
     * @param Brick       $phoneNumber          brick PhoneNumber instance providing core parsing and formatting
     *                                          functionality for international phone number handling
     * @param string      $countryCode          Numeric country calling code (e.g., "1" for US, "46" for Sweden).
     * @param null|string $geographicalAreaCode area code component identifying geographic region within the country,
     *                                          or null if not applicable to the number type
     * @param null|string $nationalNumber       national significant number without country code or formatting,
     *                                          representing the complete subscriber number
     * @param null|string $regionCode           ISO 3166-1 alpha-2 country code (e.g., "US", "SE") identifying
     *                                          the phone number's geographic region, or null if unknown.
     * @param null|int    $numberType           Numeric identifier for phone number type (mobile, fixed-line, etc.)
     *                                          based on libphonenumber classification, or null if undetermined.
     * @param bool        $isPossible           indicates whether the number matches possible length patterns
     *                                          for its region, without full validity verification
     * @param bool        $isValid              indicates whether the number passes complete validation including
     *                                          format rules, length requirements, and region-specific patterns
     */
    public function __construct(
        public readonly Brick $phoneNumber,
        public readonly string $countryCode,
        public readonly ?string $geographicalAreaCode,
        public readonly ?string $nationalNumber,
        public readonly ?string $regionCode,
        public readonly ?int $numberType,
        public readonly bool $isPossible,
        public readonly bool $isValid,
    ) {}

    /**
     * Convert the phone number to E.164 format string representation.
     *
     * @return string The phone number in E.164 format (e.g., "+46701234567")
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a phone number instance from a string value.
     *
     * Parses the provided phone number string using the specified region code
     * as context for parsing ambiguous numbers. Extracts all metadata including
     * country code, area code, number type, and validation status.
     *
     * @param string $value      the phone number string to parse, which may include formatting
     *                           characters, country codes, or be in local format
     * @param string $regionCode ISO 3166-1 alpha-2 country code used as parsing context
     *                           for numbers without explicit country codes (defaults to "SE")
     *
     * @throws PhoneNumberParseException when the phone number cannot be parsed or is invalid
     *
     * @return self A validated phone number instance with extracted metadata
     */
    public static function createFromString(string $value, string $regionCode = 'SE'): self
    {
        $phoneNumber = Brick::parse($value, $regionCode);

        $geographicalAreaCode = $phoneNumber->getGeographicalAreaCode();

        return new self(
            phoneNumber: $phoneNumber,
            countryCode: $phoneNumber->getCountryCode(),
            geographicalAreaCode: $geographicalAreaCode === '' ? null : $geographicalAreaCode,
            nationalNumber: $phoneNumber->getNationalNumber(),
            regionCode: $phoneNumber->getRegionCode(),
            numberType: $phoneNumber->getNumberType()->value,
            isPossible: $phoneNumber->isPossibleNumber(),
            isValid: $phoneNumber->isValidNumber(),
        );
    }

    /**
     * Format the phone number using a specific format standard.
     *
     * @param  PhoneNumberFormat $format The desired output format (E164, INTERNATIONAL,
     *                                   NATIONAL, or RFC3966)
     * @return string            The formatted phone number string
     */
    public function format(PhoneNumberFormat $format): string
    {
        return $this->phoneNumber->format($format);
    }

    /**
     * Format the phone number for international dialing from a specific region.
     *
     * Produces the appropriate dialing format when calling this number from
     * the specified country, including international prefixes when necessary.
     *
     * @param  string $regionCode ISO 3166-1 alpha-2 country code representing
     *                            the calling location (e.g., "US", "SE")
     * @return string The formatted phone number optimized for dialing from the specified region
     */
    public function formatForCallingFrom(string $regionCode): string
    {
        return $this->phoneNumber->formatForCallingFrom($regionCode);
    }

    /**
     * Compare this phone number with another for equality.
     *
     * @param  self $other The phone number to compare against
     * @return bool True if both numbers have identical E.164 representations
     */
    public function isEqualTo(self $other): bool
    {
        return $this->toString() === $other->toString();
    }

    /**
     * Get the phone number in E.164 format.
     *
     * @return string The phone number in E.164 format (e.g., "+46701234567")
     */
    public function toString(): string
    {
        return $this->format(PhoneNumberFormat::E164);
    }

    /**
     * Serialize the phone number to JSON representation.
     *
     * @return array{phoneNumber: string} Array containing the E.164 formatted phone number
     */
    #[Override()]
    public function jsonSerialize(): array
    {
        return [
            'phoneNumber' => $this->toString(),
        ];
    }
}
