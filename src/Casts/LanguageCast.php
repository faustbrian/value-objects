<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Casts;

use Attribute;
use Cline\ValueObjects\Language as LanguageValueObject;
use Override;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

use function is_string;

/**
 * Casts string values to Language value objects for Laravel Data properties.
 *
 * This cast transforms raw string language codes into structured Language value objects.
 * Returns Uncastable when the input value is not a string, allowing Laravel Data to
 * handle validation and fallback behavior according to property configuration.
 *
 * ```php
 * use Cline\Data\Casts\LanguageCast;
 *
 * final class UserData extends Data
 * {
 *     public function __construct(
 *         #[LanguageCast]
 *         public LanguageValueObject $language,
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
final class LanguageCast implements Cast
{
    /**
     * Transforms a string language code into a Language value object.
     *
     * @param  DataProperty                   $property   The property being cast (unused but required by interface)
     * @param  mixed                          $value      The raw value to cast, expected to be a string language code
     * @param  array<string, mixed>           $properties All properties being cast in the current context
     * @param  CreationContext                $context    Metadata about the data object creation process
     * @return LanguageValueObject|Uncastable The Language value object if value is a string,
     *                                        or Uncastable if casting cannot be performed
     */
    #[Override()]
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): LanguageValueObject|Uncastable
    {
        if (!is_string($value)) {
            return Uncastable::create();
        }

        return LanguageValueObject::createFromString($value);
    }
}
