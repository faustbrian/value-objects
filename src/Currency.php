<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Override;
use Stringable;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

/**
 * Represents an ISO 4217 currency with comprehensive formatting and precision rules.
 *
 * Immutable value object that encapsulates currency information including code,
 * name, symbol, and various rounding and precision rules for both general and
 * cash transactions. Uses Symfony's internationalization component for data.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class Currency extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new currency value object.
     *
     * @param string            $code                  ISO 4217 three-letter currency code (e.g., "USD", "EUR", "GBP")
     * @param string            $name                  Localized human-readable currency name (e.g., "US Dollar", "Euro")
     * @param string            $symbol                Currency symbol used for display purposes (e.g., "$", "€", "£")
     * @param non-negative-int  $fractionDigits        Number of decimal places used for standard currency precision,
     *                                                 typically 2 for most currencies but varies (e.g., JPY uses 0)
     * @param positive-int      $roundingIncrement     The smallest unit increment for rounding operations in standard
     *                                                 transactions, usually 1 but can differ for certain currencies
     * @param non-negative-int  $cashFractionDigits    Number of decimal places used for cash transaction precision,
     *                                                 often differs from electronic transactions due to physical currency limitations
     * @param positive-int      $cashRoundingIncrement The smallest unit increment for rounding cash transactions,
     *                                                 accounts for smallest physical coin denominations available
     * @param null|positive-int $numericCode           ISO 4217 numeric currency code, a three-digit identifier
     *                                                 (e.g., 840 for USD), or null if not available for the currency
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $symbol,
        /** @var non-negative-int */
        public readonly int $fractionDigits,
        /** @var positive-int */
        public readonly int $roundingIncrement,
        /** @var non-negative-int */
        public readonly int $cashFractionDigits,
        /** @var positive-int */
        public readonly int $cashRoundingIncrement,
        /** @var null|positive-int */
        public readonly ?int $numericCode,
    ) {}

    /**
     * Convert the currency to its string representation.
     *
     * @return string ISO 4217 currency code
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->code;
    }

    /**
     * Create a currency instance from an ISO 4217 currency code.
     *
     * Retrieves all currency metadata including name, symbol, precision rules,
     * and rounding increments from Symfony's internationalization component.
     * Gracefully handles missing numeric codes by setting them to null.
     *
     * @param  string $value ISO 4217 three-letter currency code (e.g., "USD", "EUR")
     * @return self   New immutable currency instance with complete metadata
     */
    public static function createFromString(string $value): self
    {
        try {
            $numericCode = Currencies::getNumericCode($value);
        } catch (MissingResourceException) {
            $numericCode = null;
        }

        return new self(
            code: $value,
            name: Currencies::getName($value),
            symbol: Currencies::getSymbol($value),
            fractionDigits: Currencies::getFractionDigits($value),
            roundingIncrement: Currencies::getRoundingIncrement($value),
            cashFractionDigits: Currencies::getCashFractionDigits($value),
            cashRoundingIncrement: Currencies::getCashRoundingIncrement($value),
            numericCode: $numericCode,
        );
    }

    /**
     * Determine if this currency is equal to another currency.
     *
     * Comparison is based on the ISO 4217 currency code, which uniquely
     * identifies each currency.
     *
     * @param  self $other Currency instance to compare against
     * @return bool True if both currencies have the same code
     */
    public function isEqualTo(self $other): bool
    {
        return $this->code === $other->code;
    }

    /**
     * Convert the currency to its string representation.
     *
     * @return string ISO 4217 currency code
     */
    public function toString(): string
    {
        return $this->code;
    }
}
