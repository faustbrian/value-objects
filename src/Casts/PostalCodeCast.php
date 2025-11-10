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
 * Casts string values to PostalCode value objects with country-specific validation.
 *
 * This cast transforms raw postal code strings into structured PostalCode value objects
 * that apply country-specific format validation and normalization. Accepts an optional
 * country code parameter, or attempts to resolve it from sibling properties in the data
 * object (useful when postal codes are part of address data with a country field).
 *
 * ```php
 * use Cline\Data\Casts\PostalCodeCast;
 *
 * final class AddressData extends Data
 * {
 *     public function __construct(
 *         #[PostalCodeCast]
 *         public PostalCodeValueObject $postalCode, // Infers country from $countryCode property
 *         public CountryValueObject $countryCode, // Must have ->alpha2 property
 *         #[PostalCodeCast(countryCode: 'US')]
 *         public PostalCodeValueObject $zipCode, // Explicitly uses US validation
 *     ) {}
 * }
 * ```
 *
 * @deprecated this cast provides no logic beyond delegating to the value object factory
 *             and can be replaced with direct value object transformation in Data classes
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
#[Attribute()]
final readonly class PostalCodeCast implements Cast
{
    /**
     * Create a new PostalCodeCast instance with optional country code.
     *
     * @param null|string $countryCode ISO 3166-1 alpha-2 country code for postal code validation.
     *                                 If null, the cast attempts to resolve the country code
     *                                 from a 'countryCode' property in the same data object by
     *                                 accessing its ->alpha2 property. This allows flexible country
     *                                 code resolution when postal codes are part of address data.
     */
    public function __construct(
        private ?string $countryCode = null,
    ) {}

    /**
     * Transforms a string postal code into a PostalCode value object.
     *
     * Resolves the country code from constructor parameter or sibling properties,
     * then creates a PostalCode value object with country-specific validation.
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
     * @return PostalCodeValueObject|Uncastable The PostalCode value object if value is a string,
     *                                          or Uncastable if casting cannot be performed
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): PostalCodeValueObject|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        $code = $this->countryCode ?? ($properties['countryCode']?->alpha2 ?? null);

        return PostalCodeValueObject::createFromString($value, $code);
    }
}
