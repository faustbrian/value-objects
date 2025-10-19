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
enum LengthUnit: string
{
    case CENTIMETER = 'CM';
    case FOOT = 'FT';
    case LINEAR_METER = 'LNM';
    case METER = 'M';
    case YARD = 'YD';
}
