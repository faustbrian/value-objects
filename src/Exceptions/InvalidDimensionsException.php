<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Exceptions;

use InvalidArgumentException;

use function sprintf;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidDimensionsException extends InvalidArgumentException
{
    public static function create(float|int $length, float|int $width, float|int $height): self
    {
        return new self(sprintf('Invalid dimensions: length=%s, width=%s, height=%s', $length, $width, $height));
    }
}
