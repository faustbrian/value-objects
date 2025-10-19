<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Exceptions\Internationalization;

use InvalidArgumentException;

/**
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidCountryCodeException extends InvalidArgumentException
{
    public static function create(string $value): self
    {
        return new self('Invalid country code: '.$value);
    }
}
