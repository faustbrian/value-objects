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
enum MassUnit: string
{
    case CARAT = 'AR';
    case GRAM = 'G';
    case KILOGRAM = 'KG';
    case MILLIGRAM = 'MG';
    case OUNCE = 'OZ';
    case POUND = 'LB';
    case STONE = 'ST';
    case TON = 'T';
}
