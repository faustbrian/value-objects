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

use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function method_exists;

/**
 * Eloquent cast for strongly typed ID value objects
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 *
 * @implements CastsAttributes<StronglyTypedId, string>
 */
final readonly class StronglyTypedIdCast implements CastsAttributes, SerializesCastableAttributes
{
    public function __construct(
        private string $type,
    ) {}

    public function get($model, string $key, $value, array $attributes): ?StronglyTypedId
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof StronglyTypedId) {
            return $value;
        }

        return new $this->type($value);
    }

    /**
     * @param mixed $model
     * @param mixed $value
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof StronglyTypedId) {
            return $value->value;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return null;
    }

    public function serialize($model, string $key, $value, array $attributes)
    {
        if ($value instanceof StronglyTypedId) {
            return $value->value;
        }

        return $value;
    }
}
