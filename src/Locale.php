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
use Symfony\Component\Intl\Locales;

/**
 * Immutable value object representing a locale identifier with localized name.
 *
 * Uses Symfony Intl component to validate locale identifiers and provide
 * localized locale names. Ensures type-safe handling of locale information
 * for internationalization and localization throughout the application.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class Locale extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new locale value object.
     *
     * @param string $value     The locale identifier (e.g., 'en_US', 'fr_FR', 'de_DE').
     *                          Must be a valid locale identifier recognized by Symfony Intl.
     *                          Used for programmatic identification and configuration of
     *                          locale-specific behavior throughout the application.
     * @param string $localized The human-readable, localized name of the locale in the
     *                          current locale context (e.g., 'English (United States)',
     *                          'French (France)'). Used for display in user interfaces
     *                          and configuration screens.
     */
    public function __construct(
        public readonly string $value,
        public readonly string $localized,
    ) {}

    /**
     * Convert the locale value object to its string representation.
     *
     * @return string The locale identifier
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a locale value object from a locale identifier string.
     *
     * Validates the locale identifier using Symfony Intl component and retrieves
     * the localized locale name. This ensures only valid locale identifiers are
     * accepted while providing human-readable representations for display.
     *
     * @param  string $value The locale identifier (e.g., 'en_US', 'fr_FR', 'de_DE')
     * @return self   The locale value object with identifier and localized name
     */
    public static function createFromString(string $value): self
    {
        return new self(
            value: $value,
            localized: Locales::getName($value),
        );
    }

    /**
     * Check if this locale value object equals another.
     *
     * Equality is determined by comparing the locale identifiers, not the
     * localized names, as different display contexts may produce different
     * localized representations of the same locale.
     *
     * @param  self $other The locale value object to compare against
     * @return bool True if the locale identifiers are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the locale identifier as a string.
     *
     * @return string The locale identifier (e.g., 'en_US', 'fr_FR')
     */
    public function toString(): string
    {
        return $this->value;
    }
}
