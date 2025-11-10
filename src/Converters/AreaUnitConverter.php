<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Converters;

use Cline\ValueObjects\Enums\AreaUnit;

/**
 * @author Brian Faust <brian@shipit.fi>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class AreaUnitConverter
{
    private const array CONVERSION_FACTORS = [
        'CM2' => 0.000_1,
        'DM2' => 0.01,
        'FT2' => 0.092_903,
        'IN2' => 0.000_645_16,
        'M2' => 1,
        'MM2' => 0.000_001,
        'YD2' => 0.836_127,
    ];

    public static function convert(float $data, AreaUnit $from, AreaUnit $to): float
    {
        $dataInSquareMeters = $data * self::CONVERSION_FACTORS[$from->value];

        return $dataInSquareMeters / self::CONVERSION_FACTORS[$to->value];
    }
}
