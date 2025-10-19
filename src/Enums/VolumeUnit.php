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
enum VolumeUnit: string
{
    case BARREL = 'BBL';
    case CUBIC_CENTIMETER = 'CM3';
    case CUBIC_DECIMETER = 'DM3';
    case CUBIC_FOOT = 'FT3';
    case CUBIC_INCH = 'IN3';
    case CUBIC_METER = 'M3';
    case GALLON = 'GAL';
    case LITER = 'L';
    case PINT = 'PT';
}
