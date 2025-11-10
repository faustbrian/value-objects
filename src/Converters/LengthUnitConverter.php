<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Converters;

use Cline\ValueObjects\Enums\LengthUnit;

/**
 * @author Brian Faust <brian@shipit.fi>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class LengthUnitConverter
{
    private const array CONVERSION_FACTORS = [
        'CM' => 0.01,
        'FT' => 0.304_8,
        'LNM' => 1,
        'M' => 1,
        'YD' => 0.914_4,
    ];

    public static function convert(float $data, LengthUnit $from, LengthUnit $to): float
    {
        $dataInMeters = $data * self::CONVERSION_FACTORS[$from->value];

        return $dataInMeters / self::CONVERSION_FACTORS[$to->value];
    }
}
