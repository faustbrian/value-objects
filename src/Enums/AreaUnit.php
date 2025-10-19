<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust - All Rights Reserved
 *
 * Unauthorized copying, distribution, or use of this file in any manner
 * is strictly prohibited. This material is proprietary and confidential.
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
