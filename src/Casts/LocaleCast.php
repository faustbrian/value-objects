<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Casts;

use Attribute;
use Cline\ValueObjects\Locale as LocaleValueObject;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * Casts string values to Locale value objects for Laravel Data properties.
 *
 * This cast transforms raw string locale identifiers (e.g., "en_US", "fr_FR") into
 * structured Locale value objects. Returns Uncastable when the input value is not
 * a string, allowing Laravel Data to handle validation and fallback behavior.
 *
 * ```php
 * use Cline\Data\Casts\LocaleCast;
 *
 * final class UserSettingsData extends Data
 * {
 *     public function __construct(
 *         #[LocaleCast]
 *         public LocaleValueObject $locale,
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
final class LocaleCast implements Cast
{
    /**
     * Transforms a string locale identifier into a Locale value object.
     *
     * @param  DataProperty                 $property   The property being cast (unused but required by interface)
     * @param  mixed                        $value      The raw value to cast, expected to be a string locale identifier
     * @param  array<string, mixed>         $properties All properties being cast in the current context
     * @param  CreationContext              $context    Metadata about the data object creation process
     * @return LocaleValueObject|Uncastable The Locale value object if value is a string,
     *                                      or Uncastable if casting cannot be performed
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): LocaleValueObject|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        return LocaleValueObject::createFromString($value);
    }
}
