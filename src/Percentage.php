<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Illuminate\Support\Number;
use Stringable;

/**
 * Immutable value object representing a percentage with configurable formatting.
 *
 * Encapsulates a numeric percentage value with formatting options for precision
 * and locale-specific display. Uses Laravel's Number helper for consistent
 * percentage formatting across the application.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final class Percentage extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new percentage value object.
     *
     * @param float       $number       The numeric percentage value (e.g., 45.5 for 45.5%). Stored
     *                                  as a float to maintain precision. Can represent any positive
     *                                  or negative percentage value.
     * @param int         $precision    The number of decimal places to display (e.g., 2 for 45.50%).
     *                                  Controls the minimum number of digits after the decimal point
     *                                  in formatted output. Must be a non-negative integer.
     * @param null|int    $maxPrecision Optional maximum number of decimal places. When set, allows
     *                                  trailing zeros to be trimmed up to this limit (e.g., 45.5%
     *                                  instead of 45.50%). Useful for cleaner display of round numbers.
     * @param null|string $locale       Optional locale identifier for formatting (e.g., 'en_US', 'de_DE').
     *                                  When null, uses the application's default locale. Affects decimal
     *                                  separator and percent symbol placement.
     */
    public function __construct(
        public readonly float $number,
        public readonly int $precision,
        public readonly ?int $maxPrecision,
        public readonly ?string $locale,
    ) {}

    /**
     * Convert the percentage value object to its string representation.
     *
     * @return string The formatted percentage with symbol (e.g., "45.5%")
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a percentage value object from a numeric value with formatting options.
     *
     * Provides a convenient factory method for creating percentage instances
     * with customizable formatting. Allows specification of precision, maximum
     * precision, and locale for display purposes.
     *
     * @param  float       $number       The numeric percentage value (e.g., 45.5 for 45.5%)
     * @param  int         $precision    Number of decimal places (defaults to 0 for whole percentages)
     * @param  null|int    $maxPrecision Maximum decimal places to trim trailing zeros
     * @param  null|string $locale       Locale for formatting (null uses application default)
     * @return self        The percentage value object with specified formatting
     */
    public static function createFromNumber(
        float $number,
        int $precision = 0,
        ?int $maxPrecision = null,
        ?string $locale = null,
    ): self {
        return new self($number, $precision, $maxPrecision, $locale);
    }

    /**
     * Get the raw numeric percentage value.
     *
     * Returns the underlying float value without formatting. Useful for
     * calculations or comparisons that require the numeric representation.
     *
     * @return float The percentage as a numeric value
     */
    public function getNumber(): float
    {
        return $this->number;
    }

    /**
     * Check if this percentage value equals another.
     *
     * Equality is determined by comparing the numeric values only. Formatting
     * options (precision, locale) are not considered in the comparison.
     *
     * @param  self $other The percentage value object to compare against
     * @return bool True if the numeric values are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->number === $other->number;
    }

    /**
     * Get the formatted percentage as a string.
     *
     * Applies the configured precision, maximum precision, and locale settings
     * to produce a properly formatted percentage string using Laravel's Number helper.
     *
     * @return string The formatted percentage with symbol (e.g., "45.5%", "45.50%")
     */
    public function toString(): string
    {
        return (string) Number::percentage($this->number, $this->precision, $this->maxPrecision, $this->locale);
    }
}
