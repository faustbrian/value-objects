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
enum CountUnit: string
{
    case DOZEN = 'DOZ';
    case DOZEN_PAIR = 'DPR';
    case EACH = 'EA';
    case GROSS = 'GR';
    case NUMBER = 'NO';
    case PAIR = 'PR';
    case PIECES = 'PCS';
}
