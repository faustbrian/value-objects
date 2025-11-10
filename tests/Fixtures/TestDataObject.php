<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use Cline\ValueObjects\Currency;
use Spatie\LaravelData\Data;

/**
 * Test data object for testing casts.
 *
 * @internal
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TestDataObject extends Data
{
    public function __construct(
        public readonly ?Currency $currency = null,
        public readonly mixed $value = null,
    ) {}
}
