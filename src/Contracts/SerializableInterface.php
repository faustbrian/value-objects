<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Contracts;

use JsonSerializable;

/**
 * Interface for value objects that can be serialized to JSON.
 *
 * Extends PHP's native JsonSerializable interface to ensure value objects
 * can be properly encoded to JSON for API responses, storage, and data
 * interchange. Implementations should return a JSON-safe representation
 * of the value object's state.
 *
 * @author Brian Faust <brian@cline.sh>
 */
interface SerializableInterface extends JsonSerializable
{
    /**
     * Get the value to be serialized to JSON.
     *
     * Returns a JSON-safe representation of the value object. This should
     * typically be a scalar value, array, or stdClass that accurately
     * represents the object's state without circular references.
     *
     * @return mixed the JSON-safe value to be encoded
     */
    public function jsonSerialize(): mixed;
}
