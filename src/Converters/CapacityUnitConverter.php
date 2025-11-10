<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Converters;

use Cline\ValueObjects\Enums\CapacityUnit;

/**
 * @author Brian Faust <brian@shipit.fi>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class CapacityUnitConverter
{
    private const array CONVERSION_FACTORS = [
        'BBL' => 158.987,
        'CL' => 0.01,
        'CM3' => 0.001,
        'DM3' => 1,
        'FT3' => 28.316_8,
        'IN3' => 0.016_387_1,
        'M3' => 1_000,
        'DL' => 0.1,
        'DRUM' => 208.197,
        'GAL' => 3.785_41,
        'L' => 1,
        'ML' => 0.001,
        'PT' => 0.473_176,
        'QT' => 0.946_353,
    ];

    public static function convert(float $data, CapacityUnit $from, CapacityUnit $to): float
    {
        $dataInLiters = $data * self::CONVERSION_FACTORS[$from->value];

        return $dataInLiters / self::CONVERSION_FACTORS[$to->value];
    }
}
