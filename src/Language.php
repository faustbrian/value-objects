<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidLanguageCodeException;
use Override;
use Stringable;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Languages;

/**
 * Immutable value object representing an ISO 639 language code with localized name.
 *
 * Uses Symfony Intl component to validate language codes and provide localized
 * language names. Ensures type-safe handling of language identifiers throughout
 * the application while maintaining human-readable representations.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class Language extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new language value object.
     *
     * @param string $value     The ISO 639 language code (e.g., 'en', 'fr', 'de'). Must be
     *                          a valid language code recognized by Symfony Intl. Used for
     *                          programmatic identification and storage of language settings.
     * @param string $localized The human-readable, localized name of the language in the
     *                          current locale (e.g., 'English', 'French', 'German'). Used
     *                          for display purposes in user interfaces and reports.
     */
    public function __construct(
        public readonly string $value,
        public readonly string $localized,
    ) {}

    /**
     * Convert the language value object to its string representation.
     *
     * @return string The ISO 639 language code
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a language value object from an ISO 639 language code.
     *
     * Validates the language code using Symfony Intl component and retrieves
     * the localized language name. This ensures only valid language codes
     * are accepted while providing human-readable representations.
     *
     * @param string $value The ISO 639 language code (e.g., 'en', 'fr', 'de')
     *
     * @throws InvalidLanguageCodeException When the language code is not recognized by Symfony Intl
     *
     * @return self The language value object with code and localized name
     */
    public static function createFromString(string $value): self
    {
        try {
            return new self(
                value: $value,
                localized: Languages::getName($value),
            );
        } catch (MissingResourceException) {
            throw InvalidLanguageCodeException::create($value);
        }
    }

    /**
     * Check if this language value object equals another.
     *
     * Equality is determined by comparing the ISO 639 language codes,
     * not the localized names, as different locales may produce different
     * localized representations of the same language.
     *
     * @param  self $other The language value object to compare against
     * @return bool True if the language codes are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the ISO 639 language code as a string.
     *
     * @return string The language code (e.g., 'en', 'fr', 'de')
     */
    public function toString(): string
    {
        return $this->value;
    }
}
