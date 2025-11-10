<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Algorithms\Luhn;
use Cline\ValueObjects\Exceptions\Validation\InvalidBarcodeException;
use Illuminate\Support\Str;
use Override;
use Stringable;

use function ctype_alnum;
use function mb_strlen;
use function mb_substr;
use function throw_if;
use function throw_unless;

/**
 * Global Returnable Asset Identifier (GRAI) for tracking reusable transport items.
 *
 * GRAI is a GS1 standard identifier for reusable assets such as pallets, crates,
 * containers, and other returnable transport items. The structure includes:
 * - Leading zero indicator digit (must be '0')
 * - 13-digit base identifier validated with Luhn algorithm
 * - Optional alphanumeric serial component (up to 16 characters)
 * Total length ranges from 13 to 30 characters after formatting removal.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/grai GS1 GRAI Documentation
 *
 * @psalm-immutable
 */
final class GRAI extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new GRAI instance.
     *
     * @param string $value The GRAI string value, stored with original formatting
     *                      (hyphens, spaces) intact for display purposes. Must start
     *                      with '0' indicator digit and be 13-30 chars after stripping.
     */
    public function __construct(
        public readonly string $value,
    ) {}

    /**
     * Convert the GRAI to its string representation.
     *
     * @return string the GRAI value as-is, including any formatting characters
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a GRAI from a string value with comprehensive validation.
     *
     * Performs multiple validation checks to ensure GS1 compliance:
     * 1. Minimum length of 13 characters
     * 2. First digit must be '0' (GRAI indicator)
     * 3. Maximum length of 30 characters (after stripping formatting)
     * 4. Serial component (after position 13) must be alphanumeric
     * 5. First 13 digits must pass Luhn checksum validation
     *
     * @param string $value The GRAI string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GRAI length is invalid (< 13 or > 30 characters),
     *                                 first digit is not '0', serial component contains invalid
     *                                 characters, or Luhn validation fails
     *
     * @return self a validated GRAI instance
     */
    public static function createFromString(string $value): self
    {
        throw_if(mb_strlen($value) < 13, InvalidBarcodeException::invalid('GRAI', $value));

        /** @var string $valueStripped */
        $valueStripped = Str::replace(['‐', '-', ' '], '', $value);

        // GRAI must start with '0' indicator digit
        throw_if(0 !== (int) $valueStripped[0], InvalidBarcodeException::invalid('GRAI', $value));

        // Remove the leading '0' for further validation
        $valueStripped = mb_substr($valueStripped, 1, mb_strlen($valueStripped) - 1);

        throw_if(mb_strlen($valueStripped) > 29, InvalidBarcodeException::invalid('GRAI', $value));

        // Validate that serial component (after position 13) is alphanumeric
        throw_unless(ctype_alnum(mb_substr($valueStripped, 13, mb_strlen($valueStripped))), InvalidBarcodeException::invalid('GRAI', $value));

        if (Luhn::check(mb_substr($valueStripped, 0, 13), 13)) {
            return new self($value);
        }

        throw InvalidBarcodeException::invalid('GRAI', $value);
    }

    /**
     * Compare this GRAI with another for equality.
     *
     * @param  self $other the GRAI to compare against
     * @return bool true if the GRAI values are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the GRAI value as a string.
     *
     * @return string the GRAI value as-is, including any formatting characters
     */
    public function toString(): string
    {
        return $this->value;
    }
}
