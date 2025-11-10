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
 * Global Trade Item Number (GTIN-12) for product identification in North America.
 *
 * GTIN-12 is a 12-digit GS1 identifier primarily used in North America. Also known
 * as UPC-A (Universal Product Code), it is the standard barcode format for retail
 * products in the United States and Canada. The 12 digits include a company prefix,
 * item reference, and check digit validated using the Luhn algorithm.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/gtin GS1 GTIN Documentation
 *
 * @psalm-immutable
 */
final class GTIN12 extends AbstractBarcode
{
    /**
     * Create a GTIN-12 from a string value with validation.
     *
     * Validates that the provided value is exactly 12 digits (after stripping
     * formatting characters) and passes Luhn checksum validation. The original
     * formatted value is preserved for display purposes.
     *
     * @param string $value The GTIN-12 string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GTIN-12 length is not exactly 12 digits
     *                                 or when Luhn validation fails
     *
     * @return self a validated GTIN-12 instance
     */
    public static function createFromString(string $value): self
    {
        return self::createWithValidation($value, 'GTIN-12', 12);
    }
}
