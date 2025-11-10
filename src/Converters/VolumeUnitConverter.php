<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Converters;

use Cline\ValueObjects\Enums\VolumeUnit;

/**
 * @author Brian Faust <brian@shipit.fi>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class VolumeUnitConverter
{
    private const array CONVERSION_FACTORS = [
        'BBL' => 158.987,
        'CM3' => 0.001,
        'DM3' => 1,
        'FT3' => 28.316_8,
        'GAL' => 3.785_41,
        'IN3' => 0.016_387_1,
        'L' => 1,
        'M3' => 1_000,
        'PT' => 0.473_176,
    ];

    public static function convert(float $data, VolumeUnit $from, VolumeUnit $to): float
    {
        $dataInLiters = $data * self::CONVERSION_FACTORS[$from->value];

        return $dataInLiters / self::CONVERSION_FACTORS[$to->value];
    }
}
