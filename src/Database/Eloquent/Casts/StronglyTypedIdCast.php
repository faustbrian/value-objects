<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Database\Eloquent\Casts;

use Cline\ValueObjects\StronglyTypedId;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;

/**
 * Eloquent cast for strongly typed ID value objects
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class StronglyTypedIdCast implements CastsAttributes, SerializesCastableAttributes
{
    public function __construct(
        private string $type,
    ) {}

    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof StronglyTypedId) {
            return $value;
        }

        return new $this->type($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof StronglyTypedId) {
            return $value->value;
        }

        return (string) $value;
    }

    public function serialize($model, string $key, $value, array $attributes)
    {
        if ($value instanceof StronglyTypedId) {
            return $value->value;
        }

        return $value;
    }
}
