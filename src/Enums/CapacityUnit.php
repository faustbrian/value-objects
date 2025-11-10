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
enum CapacityUnit: string
{
    case BARREL = 'BBL';
    case CENTILITER = 'CL';
    case CUBIC_CENTIMETER = 'CM3';
    case CUBIC_DECIMETER = 'DM3';
    case CUBIC_FOOT = 'FT3';
    case CUBIC_INCH = 'IN3';
    case CUBIC_METER = 'M3';
    case DECILITER = 'DL';
    case DRUM = 'DRUM';
    case GALLON = 'GAL';
    case LITER = 'L';
    case MILLILITER = 'ML';
    case PINT = 'PT';
    case QUART = 'QT';
}
