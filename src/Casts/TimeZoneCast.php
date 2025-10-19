<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Casts;

use Attribute;
use Cline\ValueObjects\TimeZone as TimeZoneValueObject;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * Converts string timezone identifiers into TimeZone value objects.
 *
 * Transforms timezone strings (e.g., "America/New_York", "UTC") into structured
 * TimeZone value objects for type-safe timezone handling. Returns Uncastable for
 * non-string values to indicate the cast cannot be applied. Useful for normalizing
 * timezone data from API requests or configuration into domain objects.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
#[Attribute()]
final class TimeZoneCast implements Cast
{
    /**
     * Transform the property value into a TimeZone value object.
     *
     * Converts valid timezone identifier strings into TimeZoneValueObject instances
     * for structured timezone representation. Non-string values return Uncastable
     * to signal that the cast cannot process the input type.
     *
     * @param  DataProperty                   $property   The property being cast (unused in this implementation)
     * @param  mixed                          $value      The timezone identifier string to convert
     * @param  array<string, mixed>           $properties All properties in the data object (unused in this implementation)
     * @param  CreationContext                $context    Context information about the data creation process (unused)
     * @return TimeZoneValueObject|Uncastable The timezone value object, or Uncastable for invalid input types
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): TimeZoneValueObject|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        return TimeZoneValueObject::createFromString($value);
    }
}
