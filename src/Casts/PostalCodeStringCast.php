<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Casts;

use Attribute;
use Brick\Postcode\InvalidPostcodeException;
use Brick\Postcode\UnknownCountryException;
use Cline\ValueObjects\PostalCode as PostalCodeValueObject;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * Casts string values to formatted postal code strings with validation.
 *
 * This cast validates and formats postal codes according to country-specific rules,
 * returning a properly formatted string representation rather than a value object.
 * Useful when you need postal codes as strings in your data layer but still want
 * validation and standardized formatting. Returns Uncastable for invalid postal codes
 * or when formatting produces an empty string.
 *
 * ```php
 * use Cline\Data\Casts\PostalCodeStringCast;
 *
 * final class AddressData extends Data
 * {
 *     public function __construct(
 *         #[PostalCodeStringCast]
 *         public string $postalCode, // "90210" validated and formatted, infers country
 *         public CountryValueObject $countryCode, // Must have ->alpha2 property
 *         #[PostalCodeStringCast(countryCode: 'GB')]
 *         public string $ukPostcode, // "SW1A 1AA" validated as UK format
 *     ) {}
 * }
 * ```
 *
 * @author Brian Faust <brian@cline.sh>
 * @deprecated this cast provides no logic beyond delegating to the value object factory
 *             and can be replaced with direct value object transformation in Data classes
 *
 * @psalm-immutable
 */
#[Attribute()]
final readonly class PostalCodeStringCast implements Cast
{
    /**
     * Create a new PostalCodeStringCast instance with optional country code.
     *
     * @param null|string $countryCode ISO 3166-1 alpha-2 country code for postal code validation
     *                                 and formatting. If null, attempts to resolve the country code
     *                                 from a 'countryCode' property in the same data object by
     *                                 accessing its ->alpha2 property. Country code determines the
     *                                 validation rules and output format for the postal code string.
     */
    public function __construct(
        private ?string $countryCode = null,
    ) {}

    /**
     * Validates and formats a postal code string according to country rules.
     *
     * Creates a PostalCode value object to perform validation, then extracts
     * the formatted string representation. Returns Uncastable if the postal code
     * is invalid or formatting produces an empty string.
     *
     * @param DataProperty         $property   The property being cast (unused but required by interface)
     * @param mixed                $value      The raw value to cast, expected to be a postal code string
     * @param array<string, mixed> $properties All properties being cast in the current context,
     *                                         may contain 'countryCode' with ->alpha2 property
     * @param CreationContext      $context    Metadata about the data object creation process
     *
     * @throws InvalidPostcodeException When the postal code format is invalid for the country
     * @throws UnknownCountryException  When the country code is not recognized
     *
     * @return string|Uncastable The formatted postal code string if valid and non-empty,
     *                           or Uncastable if casting cannot be performed
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): string|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        $pc = PostalCodeValueObject::createFromString($value, $this->countryCode ?? ($properties['countryCode']?->alpha2 ?? null));
        $formatted = $pc->toString();

        if ($formatted === '') {
            return Uncastable::create();
        }

        return $formatted;
    }
}
