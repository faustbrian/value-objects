<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Casts;

use Attribute;
use Cline\ValueObjects\Currency as CurrencyValueObject;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * Converts string currency codes to Currency value objects.
 *
 * Transforms ISO 4217 currency code strings into strongly-typed Currency value
 * objects for improved type safety and domain modeling. Returns Uncastable for
 * non-string values, allowing the cast to gracefully fail for invalid input types.
 *
 * @author Brian Faust <brian@cline.sh>
 * @deprecated This cast provides no logic and can be removed
 *
 * @psalm-immutable
 */
#[Attribute()]
final class CurrencyCast implements Cast
{
    /**
     * Casts a string currency code to a Currency value object.
     *
     * Delegates to CurrencyValueObject::createFromString() to parse and validate
     * the currency code. Non-string values return Uncastable to signal that the
     * cast cannot be performed, allowing Spatie Laravel Data to handle the failure
     * according to its validation rules.
     *
     * @param  DataProperty                   $property   The property being cast
     * @param  mixed                          $value      The value to cast (expected to be a currency code string)
     * @param  array<string, mixed>           $properties All properties in the data object
     * @param  CreationContext                $context    The creation context for the data object
     * @return CurrencyValueObject|Uncastable Currency value object if successful, Uncastable otherwise
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): CurrencyValueObject|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        return CurrencyValueObject::createFromString($value);
    }
}
