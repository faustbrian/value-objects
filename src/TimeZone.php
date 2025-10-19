<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Internationalization\InvalidTimeZoneException;
use Override;
use Stringable;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Timezones;

/**
 * Value object representing a validated time zone identifier.
 *
 * Wraps Symfony Intl component to validate time zone identifiers and provide
 * localized human-readable names. Ensures time zone values are valid according
 * to the IANA Time Zone Database.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.1
 *
 * @psalm-immutable
 */
final class TimeZone extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new time zone value object.
     *
     * @param string $value     IANA time zone identifier (e.g., "Europe/Stockholm", "America/New_York")
     * @param string $localized Human-readable, localized name of the time zone for display purposes
     */
    public function __construct(
        public readonly string $value,
        public readonly string $localized,
    ) {}

    /**
     * Convert the time zone to its string representation.
     *
     * @return string The IANA time zone identifier
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a time zone instance from an IANA identifier.
     *
     * Validates the time zone identifier against the IANA Time Zone Database
     * and retrieves the localized display name using Symfony's Intl component.
     *
     * @param string $value IANA time zone identifier (e.g., "Europe/Stockholm")
     *
     * @throws InvalidTimeZoneException when the identifier is not a valid IANA time zone
     *
     * @return self A validated time zone instance with localized name
     */
    public static function createFromString(string $value): self
    {
        try {
            return new self(
                value: $value,
                localized: Timezones::getName($value),
            );
        } catch (MissingResourceException) {
            throw InvalidTimeZoneException::create($value);
        }
    }

    /**
     * Compare this time zone with another for equality.
     *
     * @param  self $other The time zone to compare against
     * @return bool True if both time zones have identical identifiers
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the time zone identifier as a string.
     *
     * @return string The IANA time zone identifier
     */
    public function toString(): string
    {
        return $this->value;
    }
}
