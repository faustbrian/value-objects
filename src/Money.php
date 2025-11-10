<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money as Brick;
use Override;
use Stringable;

/**
 * Immutable value object representing monetary values with currency precision.
 *
 * Provides type-safe handling of money using the Brick\Money library for
 * arbitrary-precision arithmetic. Ensures accurate financial calculations
 * without floating-point errors while maintaining currency awareness.
 *
 * Key features:
 * - Arbitrary-precision arithmetic for accurate money calculations
 * - Currency-aware operations preventing invalid cross-currency operations
 * - Immutable design ensuring value objects cannot be modified
 * - Support for both major units (dollars) and minor units (cents)
 * - Comprehensive comparison and arithmetic operations
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @see https://martinfowler.com/eaaCatalog/money.html
 *
 * @psalm-immutable
 */
final readonly class Money implements Stringable
{
    /**
     * Create a new money value object.
     *
     * @param Brick        $value    The Brick\Money instance providing arbitrary-precision
     *                               arithmetic capabilities. Encapsulates the amount and
     *                               currency information with exact decimal precision.
     * @param CurrencyCode $currency The currency code value object. Cached separately from
     *                               the Brick instance for efficient access without repeated
     *                               extraction from the underlying Brick\Money object.
     */
    public function __construct(
        private Brick $value,
        private CurrencyCode $currency,
    ) {}

    /**
     * Convert the money value object to its string representation.
     *
     * @return string The formatted money amount with currency symbol
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a money value object from major units (dollars, euros, etc.).
     *
     * Major units represent the primary denomination of a currency (e.g., $10.50
     * where 10.50 is in dollars). The value is converted to the currency's minor
     * units internally for precise storage and arithmetic.
     *
     * @param float|int|string $value        The amount in major units (e.g., 10.50 for ten dollars and fifty cents)
     * @param string           $currencyCode The ISO 4217 currency code (e.g., 'USD', 'EUR', 'GBP')
     *
     * @throws NumberFormatException      When the value cannot be parsed as a valid number
     * @throws RoundingNecessaryException When the value has more decimal places than the currency supports
     * @throws UnknownCurrencyException   When the currency code is not recognized
     *
     * @return self The money value object with precise currency representation
     */
    public static function createFromMajorUnits(int|float|string $value, string $currencyCode): self
    {
        $currencyCode = CurrencyCode::createFromString($currencyCode);

        return new self(
            value: Brick::of(amount: $value, currency: $currencyCode->toString()),
            currency: $currencyCode,
        );
    }

    /**
     * Create a money value object from minor units (cents, pence, etc.).
     *
     * Minor units represent the smallest denomination of a currency (e.g., 1050
     * cents equals $10.50). This is the preferred method for precise financial
     * calculations as it avoids floating-point precision issues.
     *
     * @param float|int|string $value        The amount in minor units (e.g., 1050 for ten dollars and fifty cents)
     * @param string           $currencyCode The ISO 4217 currency code (e.g., 'USD', 'EUR', 'GBP')
     *
     * @throws NumberFormatException      When the value cannot be parsed as a valid number
     * @throws RoundingNecessaryException When the value requires rounding to fit the currency's precision
     * @throws UnknownCurrencyException   When the currency code is not recognized
     *
     * @return self The money value object with precise currency representation
     */
    public static function createFromMinorUnits(int|float|string $value, string $currencyCode): self
    {
        $currencyCode = CurrencyCode::createFromString($currencyCode);

        return new self(
            value: Brick::ofMinor(minorAmount: $value, currency: $currencyCode->toString()),
            currency: $currencyCode,
        );
    }

    /**
     * Attempt to create a money value object, trying major units first then minor units.
     *
     * This method provides a flexible creation approach when the unit type is uncertain.
     * It first attempts to parse as major units, and if rounding is necessary, falls
     * back to parsing as minor units.
     *
     * @param float|int|string $value        The amount to parse
     * @param string           $currencyCode The ISO 4217 currency code
     *
     * @throws NumberFormatException    When the value cannot be parsed as a valid number
     * @throws UnknownCurrencyException When the currency code is not recognized
     *
     * @return self The money value object
     */
    public static function tryFrom(int|float|string $value, string $currencyCode): self
    {
        try {
            return self::createFromMajorUnits($value, $currencyCode);
        } catch (RoundingNecessaryException) {
            return self::createFromMinorUnits($value, $currencyCode);
        }
    }

    /**
     * Create a zero money value object.
     *
     * Useful for initializing sums, representing empty amounts, or as a
     * starting point for accumulation operations.
     *
     * @param  string $currency The ISO 4217 currency code (defaults to 'EUR')
     * @return self   A money value object representing zero in the specified currency
     */
    public static function createZero(string $currency = 'EUR'): self
    {
        $currencyCode = CurrencyCode::createFromString($currency);

        return new self(
            value: Brick::zero($currencyCode->toString()),
            currency: $currencyCode,
        );
    }

    /**
     * Create a money value object from an array representation.
     *
     * Expects an array with 'amount_in_minor_units' and 'currency' keys.
     * This method is primarily used for deserialization from storage or APIs.
     *
     * @param  array<string, mixed> $data Array containing 'amount_in_minor_units' and 'currency' keys
     * @return self                 The reconstructed money value object
     */
    public static function createFromArray(array $data): self
    {
        /** @var float|int|string $minorUnits */
        $minorUnits = $data['amount_in_minor_units'];

        /** @var string $currencyCode */
        $currencyCode = $data['currency'];

        return self::createFromMinorUnits($minorUnits, $currencyCode);
    }

    /**
     * Add another money value to this one.
     *
     * Both money values must have the same currency. The operation maintains
     * arbitrary precision to ensure accurate financial calculations.
     *
     * @param self $other The money value to add
     *
     * @throws MathException          When the arithmetic operation fails
     * @throws MoneyMismatchException When currencies don't match
     *
     * @return self A new money value object with the sum
     */
    public function add(self $other): self
    {
        return new self(
            value: $this->value->plus($other->toValue()),
            currency: $this->currency,
        );
    }

    /**
     * Subtract another money value from this one.
     *
     * Both money values must have the same currency. The operation maintains
     * arbitrary precision to ensure accurate financial calculations.
     *
     * @param self $other The money value to subtract
     *
     * @throws MathException          When the arithmetic operation fails
     * @throws MoneyMismatchException When currencies don't match
     *
     * @return self A new money value object with the difference
     */
    public function subtract(self $other): self
    {
        return new self(
            value: $this->value->minus($other->toValue()),
            currency: $this->currency,
        );
    }

    /**
     * Multiply this money value by a numeric factor.
     *
     * Useful for calculating totals with quantities (e.g., price × quantity).
     * The operation maintains arbitrary precision and preserves the currency.
     *
     * @param float|int|string $multiplier The factor to multiply by
     *
     * @throws MathException          When the arithmetic operation fails
     * @throws MoneyMismatchException When the operation is invalid
     *
     * @return self A new money value object with the product
     */
    public function multiply(int|float|string $multiplier): self
    {
        return new self(
            value: $this->value->multipliedBy($multiplier),
            currency: $this->currency,
        );
    }

    /**
     * Divide this money value by a numeric divisor.
     *
     * Useful for splitting amounts (e.g., bill splitting, averaging).
     * The operation maintains arbitrary precision and preserves the currency.
     *
     * @param float|int|string $divisor The divisor to divide by
     *
     * @throws MathException          When the arithmetic operation fails
     * @throws MoneyMismatchException When the operation is invalid
     *
     * @return self A new money value object with the quotient
     */
    public function divide(int|float|string $divisor): self
    {
        return new self(
            value: $this->value->dividedBy($divisor),
            currency: $this->currency,
        );
    }

    /**
     * Get the absolute value of this money amount.
     *
     * Converts negative amounts to positive while preserving the magnitude.
     *
     * @return self A new money value object with the absolute value
     */
    public function abs(): self
    {
        return new self(
            value: $this->value->abs(),
            currency: $this->currency,
        );
    }

    /**
     * Get the negated value of this money amount.
     *
     * Flips the sign of the amount (positive becomes negative, negative becomes positive).
     *
     * @return self A new money value object with the negated value
     */
    public function negated(): self
    {
        return new self(
            value: $this->value->negated(),
            currency: $this->currency,
        );
    }

    /**
     * Check if this money amount is zero.
     *
     * @return bool True if the amount is exactly zero, false otherwise
     */
    public function isZero(): bool
    {
        return $this->value->isZero();
    }

    /**
     * Check if this money amount is positive (greater than zero).
     *
     * @return bool True if the amount is greater than zero, false otherwise
     */
    public function isPositive(): bool
    {
        return $this->value->isPositive();
    }

    /**
     * Check if this money amount is positive or zero.
     *
     * @return bool True if the amount is greater than or equal to zero, false otherwise
     */
    public function isPositiveOrZero(): bool
    {
        return $this->value->isPositiveOrZero();
    }

    /**
     * Check if this money amount is negative (less than zero).
     *
     * @return bool True if the amount is less than zero, false otherwise
     */
    public function isNegative(): bool
    {
        return $this->value->isNegative();
    }

    /**
     * Check if this money amount is negative or zero.
     *
     * @return bool True if the amount is less than or equal to zero, false otherwise
     */
    public function isNegativeOrZero(): bool
    {
        return $this->value->isNegativeOrZero();
    }

    /**
     * Check if this money amount is less than another.
     *
     * @param  self $other The money value to compare against
     * @return bool True if this amount is less than the other, false otherwise
     */
    public function isLessThan(self $other): bool
    {
        return $this->value->isLessThan($other->toValue());
    }

    /**
     * Check if this money amount is less than or equal to another.
     *
     * @param  self $other The money value to compare against
     * @return bool True if this amount is less than or equal to the other, false otherwise
     */
    public function isLessThanOrEqualTo(self $other): bool
    {
        return $this->value->isLessThanOrEqualTo($other->toValue());
    }

    /**
     * Check if this money amount is greater than another.
     *
     * @param  self $other The money value to compare against
     * @return bool True if this amount is greater than the other, false otherwise
     */
    public function isGreaterThan(self $other): bool
    {
        return $this->value->isGreaterThan($other->toValue());
    }

    /**
     * Check if this money amount is greater than or equal to another.
     *
     * @param  self $other The money value to compare against
     * @return bool True if this amount is greater than or equal to the other, false otherwise
     */
    public function isGreaterThanOrEqualTo(self $other): bool
    {
        return $this->value->isGreaterThanOrEqualTo($other->toValue());
    }

    /**
     * Check if this money amount equals another.
     *
     * @param  self $other The money value to compare against
     * @return bool True if amounts and currencies are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value->isEqualTo($other->toValue());
    }

    /**
     * Format the money amount according to locale conventions.
     *
     * @param  string $locale The locale identifier (e.g., 'en_US', 'de_DE')
     * @return string The formatted money string with currency symbol and proper formatting
     */
    public function format(string $locale): string
    {
        return $this->value->formatTo($locale);
    }

    /**
     * Get the amount in minor units (cents, pence, etc.).
     *
     * @return int The amount in the smallest currency unit as an integer
     */
    public function getAmountInMinorUnits(): int
    {
        return $this->value->getMinorAmount()->toInt();
    }

    /**
     * Get the amount in major units (dollars, euros, etc.).
     *
     * @return float The amount in the primary currency unit as a float
     */
    public function getAmountInMajorUnits(): float
    {
        return $this->value->getAmount()->toFloat();
    }

    /**
     * Get the ISO 4217 currency code.
     *
     * @return string The three-letter currency code (e.g., 'USD', 'EUR', 'GBP')
     */
    public function getCurrency(): string
    {
        return $this->value->getCurrency()->getCurrencyCode();
    }

    /**
     * Get the formatted amount as a string.
     *
     * @return string The formatted money amount with currency
     */
    public function getFormattedAmount(): string
    {
        return $this->toString();
    }

    /**
     * Get the raw amount value as a string.
     *
     * @return string The amount value with arbitrary precision
     */
    public function toValue(): string
    {
        return (string) $this->value->getAmount();
    }

    /**
     * Convert the money value to its string representation.
     *
     * @return string The formatted money amount with currency symbol
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Convert the money value to an array representation.
     *
     * Provides a serializable format with amounts in both minor and major units.
     * Primarily used for event sourcing serialization - prefer keeping Money
     * objects for type safety and precision in application code.
     *
     * @return array<string, mixed> Array containing amount in minor units, major units, and currency code
     */
    public function toArray(): array
    {
        return [
            'amount_in_minor_units' => $this->getAmountInMinorUnits(),
            'amount_in_major_units' => $this->getAmountInMajorUnits(),
            'currency' => $this->getCurrency(),
        ];
    }
}
