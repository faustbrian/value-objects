<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Casts;

use Attribute;
use Cline\ValueObjects\PhoneNumber as PhoneNumberValueObject;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * Casts string values to PhoneNumber value objects with country code handling.
 *
 * This cast transforms raw phone number strings into structured PhoneNumber value objects,
 * applying country-specific formatting and validation. Accepts an optional default country
 * code that applies when the phone number doesn't include international dialing codes.
 * Returns Uncastable for non-string values.
 *
 * ```php
 * use Cline\Data\Casts\PhoneNumberCast;
 *
 * final class ContactData extends Data
 * {
 *     public function __construct(
 *         #[PhoneNumberCast(defaultCountryCode: 'US')]
 *         public PhoneNumberValueObject $phone, // "(555) 123-4567" parsed as US number
 *         #[PhoneNumberCast(defaultCountryCode: 'GB')]
 *         public PhoneNumberValueObject $ukPhone, // "020 7946 0958" parsed as UK number
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
final readonly class PhoneNumberCast implements Cast
{
    /**
     * Create a new PhoneNumberCast instance with optional default country code.
     *
     * @param null|string $defaultCountryCode ISO 3166-1 alpha-2 country code to use when parsing
     *                                        phone numbers that don't include international dialing
     *                                        codes. Defaults to 'US' if not specified. Examples: 'US',
     *                                        'GB', 'DE', 'FR'. This determines number format validation
     *                                        rules and formatting output for the phone number.
     */
    public function __construct(
        private ?string $defaultCountryCode = null,
    ) {}

    /**
     * Transforms a string phone number into a PhoneNumber value object.
     *
     * Parses the phone number string using the configured country code (or 'US' default)
     * to apply country-specific formatting and validation rules.
     *
     * @param  DataProperty                      $property   The property being cast (unused but required by interface)
     * @param  mixed                             $value      The raw value to cast, expected to be a phone number string
     * @param  array<string, mixed>              $properties All properties being cast in the current context
     * @param  CreationContext                   $context    Metadata about the data object creation process
     * @return PhoneNumberValueObject|Uncastable The PhoneNumber value object if value is a string,
     *                                           or Uncastable if casting cannot be performed
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): PhoneNumberValueObject|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        $country = $this->defaultCountryCode ?? 'US';

        return PhoneNumberValueObject::createFromString($value, $country);
    }
}
