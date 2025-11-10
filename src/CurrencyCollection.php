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

use function array_keys;
use function array_unique;
use function array_values;

/**
 * Represents a collection of ISO 4217 currency codes.
 *
 * Extends Laravel's Collection to provide specialized factory methods for
 * creating currency code collections from various sources, including raw
 * arrays and Symfony's internationalization component.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.0
 *
 * @extends Collection<int, string>
 */
final class CurrencyCollection extends Collection
{
    /**
     * Create a currency collection from an array of currency codes.
     *
     * Removes duplicate currency codes and reindexes the array with
     * sequential numeric keys starting from zero.
     *
     * @param  array<int|string, string> $currencies Array of ISO 4217 currency codes
     * @return self                      New collection containing unique currency codes with sequential keys
     */
    public static function fromArray(array $currencies): self
    {
        return new self(array_values(array_unique($currencies)));
    }

    /**
     * Create a currency collection from Symfony's internationalization component.
     *
     * Retrieves all available currency codes from Symfony's Intl component,
     * providing a comprehensive list of recognized ISO 4217 currency codes.
     *
     * @return self New collection containing all available currency codes
     */
    public static function fromSymfonyIntl(): self
    {
        return new self(array_keys(Currencies::getNames()));
    }
}
