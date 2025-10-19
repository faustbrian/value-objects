<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use Cline\ValueObjects\AbstractStringValueObject;

/**
 * Alternative test fixture for testing class-based equality.
 *
 * @psalm-immutable
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class AlternativeStringValueObject extends AbstractStringValueObject
{
}