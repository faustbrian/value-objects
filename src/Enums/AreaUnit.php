<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Enums;

/**
 * @author Brian Faust <brian@shipit.fi>
 *
 * @version 1.0.0
 */
enum AreaUnit: string
{
    case SQUARE_CENTIMETER = 'CM2';
    case SQUARE_DECIMETER = 'DM2';
    case SQUARE_FOOT = 'FT2';
    case SQUARE_INCH = 'IN2';
    case SQUARE_METER = 'M2';
    case SQUARE_MILLIMETER = 'MM2';
    case SQUARE_YARD = 'YD2';
}
