<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidCurrencyCodeException;
use Override;
use Stringable;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Exception\MissingResourceException;

use function throw_if;

/**
 * Represents an ISO 4217 currency code with localized name validation.
 *
 * Immutable value object that encapsulates a currency code and its localized
 * name, enforcing validation rules to ensure both values are non-empty.
 * Uses Symfony's internationalization component for currency name resolution.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class CurrencyCode extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new currency code value object.
     *
     * Validates that both the currency code and its localized name are non-empty.
     * Throws an exception immediately if validation fails.
     *
     * @param string $value     ISO 4217 three-letter currency code (e.g., "USD", "EUR", "GBP")
     * @param string $localized Localized human-readable currency name in the current locale
     *                          (e.g., "US Dollar", "Euro", "British Pound")
     *
     * @throws InvalidCurrencyCodeException When the currency code or localized name is empty
     */
    public function __construct(
        public readonly string $value,
        public readonly string $localized,
    ) {
        throw_if($value === '' || $value === '0', InvalidCurrencyCodeException::create($value));
        throw_if($localized === '' || $localized === '0', InvalidCurrencyCodeException::create($value));
    }

    /**
     * Convert the currency code to its string representation.
     *
     * @return string ISO 4217 currency code
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a currency code instance from an ISO 4217 code string.
     *
     * Resolves the currency code to its localized name using Symfony's
     * internationalization component and validates the result.
     *
     * @param string $value ISO 4217 three-letter currency code
     *
     * @throws InvalidCurrencyCodeException When the currency code is invalid or not recognized
     *
     * @return self New immutable currency code instance
     */
    public static function createFromString(string $value): self
    {
        try {
            return new self(
                value: $value,
                localized: Currencies::getName($value),
            );
        } catch (MissingResourceException) {
            throw InvalidCurrencyCodeException::create($value);
        }
    }

    /**
     * Determine if this currency code is equal to another currency code.
     *
     * Comparison is based on the ISO 4217 currency code value.
     *
     * @param  self $other Currency code instance to compare against
     * @return bool True if both currency codes have the same value
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Convert the currency code to its string representation.
     *
     * @return string ISO 4217 currency code
     */
    public function toString(): string
    {
        return $this->value;
    }
}
