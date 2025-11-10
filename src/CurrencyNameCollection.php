<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Illuminate\Support\Collection;
use Symfony\Component\Intl\Currencies;

/**
 * Represents a collection of currency codes mapped to their localized names.
 *
 * Extends Laravel's Collection to provide specialized methods for working
 * with currency code-to-name mappings, supporting multiple locales through
 * Symfony's internationalization component.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.0
 *
 * @extends Collection<string, string>
 */
final class CurrencyNameCollection extends Collection
{
    /**
     * Create a currency name collection from an associative array.
     *
     * @param  array<string, string> $currencyNames Associative array mapping ISO 4217 currency
     *                                              codes to their localized names (e.g., ["USD" => "US Dollar"])
     * @return self                  New collection containing the currency code-to-name mappings
     */
    public static function fromArray(array $currencyNames): self
    {
        return new self($currencyNames);
    }

    /**
     * Create a currency name collection from Symfony's internationalization component.
     *
     * Retrieves all currency names localized for the specified locale. If no locale
     * is provided, uses the default locale configured in Symfony's Intl component.
     *
     * @param  null|string $locale Locale code (e.g., "en", "de", "fr") or null for default locale
     * @return self        New collection containing all currency codes mapped to localized names
     */
    public static function fromSymfonyIntl(?string $locale = null): self
    {
        /** @var array<string, string> $names */
        $names = Currencies::getNames($locale);

        return new self($names);
    }

    /**
     * Get the localized name for a currency code.
     *
     * @param  string      $currencyCode ISO 4217 three-letter currency code
     * @return null|string Localized currency name or null if not found in collection
     */
    public function getName(string $currencyCode): ?string
    {
        return $this->get($currencyCode);
    }

    /**
     * Determine if the collection contains a name for the given currency code.
     *
     * @param  string $currencyCode ISO 4217 three-letter currency code
     * @return bool   True if the collection contains a name for the currency code
     */
    public function hasName(string $currencyCode): bool
    {
        return $this->has($currencyCode);
    }
}
