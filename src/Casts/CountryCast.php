<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Casts;

use Attribute;
use Cline\ValueObjects\Country as CountryValueObject;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * Converts string country codes to Country value objects.
 *
 * Transforms ISO country code strings into strongly-typed Country value objects
 * for improved type safety and domain modeling. Returns Uncastable for non-string
 * values, allowing the cast to gracefully fail for invalid input types.
 *
 * @deprecated This cast provides no logic and can be removed
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
#[Attribute()]
final class CountryCast implements Cast
{
    /**
     * Casts a string country code to a Country value object.
     *
     * Delegates to CountryValueObject::createFromString() to parse and validate
     * the country code. Non-string values return Uncastable to signal that the
     * cast cannot be performed, allowing Spatie Laravel Data to handle the failure
     * according to its validation rules.
     *
     * @param  DataProperty                  $property   The property being cast
     * @param  mixed                         $value      The value to cast (expected to be a country code string)
     * @param  array<string, mixed>          $properties All properties in the data object
     * @param  CreationContext               $context    The creation context for the data object
     * @return CountryValueObject|Uncastable Country value object if successful, Uncastable otherwise
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): CountryValueObject|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        return CountryValueObject::createFromString($value);
    }
}
