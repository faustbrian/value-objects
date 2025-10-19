<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Algorithms;

use Spatie\Regex\Regex;

use function is_string;
use function mb_strlen;
use function mb_substr;
use function sprintf;

/**
 * Luhn algorithm implementation for barcode validation.
 *
 * The Luhn algorithm is a checksum formula used to validate identification
 * numbers such as barcodes (GTIN, EAN, UPC), credit card numbers, and other
 * numeric codes. It detects simple errors in digit sequences.
 *
 * @see https://en.wikipedia.org/wiki/Luhn_algorithm
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final class Luhn
{
    /**
     * Validate a numeric string using the Luhn algorithm.
     *
     * The algorithm multiplies alternating digits by the multiplier (default 3),
     * sums all digits, and checks if the result is divisible by the divisor
     * (default 10). The alternation pattern depends on whether the length is
     * even or odd.
     *
     * @param  mixed $value      the value to validate, will be cast to string if not already
     * @param  int   $length     expected length of the numeric string after casting
     * @param  int   $divisor    the modulo divisor for final validation, typically 10
     * @param  int   $multiplier the factor applied to alternating digits, typically 3
     * @return bool  true if the value passes Luhn validation, false otherwise
     */
    public static function check(mixed $value, int $length, int $divisor = 10, int $multiplier = 3): bool
    {
        if (is_string($value)) {
            $stringValue = $value;
        } else {
            /** @phpstan-ignore-next-line cast.string -- Value is non-null and castable to string after type check */
            $stringValue = (string) $value;
        }

        $value = $stringValue;

        if (mb_strlen($value) !== $length) {
            return false;
        }

        if (!Regex::match(sprintf('/\\d{%d}/i', $length), $value)->hasMatch()) {
            return false;
        }

        if ((int) $value === 0) {
            return false;
        }

        $sum = 0;

        // Apply multiplier to alternating digits based on length parity
        for ($i = 0; $i < $length; $i += 2) {
            if (0 === $length % 2) {
                $sum += $multiplier * (int) mb_substr($value, $i, 1);
                $sum += (int) mb_substr($value, $i + 1, 1);
            } else {
                $sum += (int) mb_substr($value, $i, 1);
                $sum += $multiplier * (int) mb_substr($value, $i + 1, 1);
            }
        }

        return 0 === $sum % $divisor;
    }
}
