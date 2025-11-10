<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Exceptions\Serialization;

use InvalidArgumentException;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class NotJsonException extends InvalidArgumentException
{
    public static function value(string $value): self
    {
        return new self('Invalid JSON: '.$value);
    }
}
