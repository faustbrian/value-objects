<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Exceptions\Validation;

use InvalidArgumentException;

use function sprintf;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidBarcodeException extends InvalidArgumentException
{
    public static function invalid(string $type, string $value): self
    {
        return new self(sprintf('Invalid %s: %s', $type, $value));
    }
}
