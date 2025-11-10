<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\ValueObjects\Exceptions\Validation\InvalidBarcodeException;

/**
 * Global Location Number (GLN) for uniquely identifying physical or legal entities.
 *
 * GLN is a GS1 standard 13-digit identifier used to identify parties and locations
 * in supply chains. Used for identifying companies, warehouses, distribution centers,
 * retail stores, and other physical or functional locations. Validated using the
 * Luhn algorithm as defined in GS1 specifications.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/gln GS1 GLN Documentation
 *
 * @psalm-immutable
 */
final class GLN extends AbstractBarcode
{
    /**
     * Create a GLN from a string value with validation.
     *
     * Validates that the provided value is exactly 13 digits (after stripping
     * formatting characters) and passes Luhn checksum validation. The original
     * formatted value is preserved for display purposes.
     *
     * @param string $value The GLN string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GLN length is not exactly 13 digits
     *                                 or when Luhn validation fails
     *
     * @return self a validated GLN instance
     */
    public static function createFromString(string $value): self
    {
        return self::createWithValidation($value, 'GLN', 13);
    }
}
