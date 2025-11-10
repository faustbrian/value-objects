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
 * Global Trade Item Number (GTIN-8) for compact product identification.
 *
 * GTIN-8 is an 8-digit GS1 identifier used for small retail products where space
 * constraints prevent the use of standard GTIN-13 barcodes. Also known as EAN-8,
 * it is primarily used for small items like chewing gum, cosmetics, and other
 * products with limited packaging surface area. The 8 digits include a GS1
 * prefix, item reference, and check digit validated using the Luhn algorithm.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/gtin GS1 GTIN Documentation
 *
 * @psalm-immutable
 */
final class GTIN8 extends AbstractBarcode
{
    /**
     * Create a GTIN-8 from a string value with validation.
     *
     * Validates that the provided value is exactly 8 digits (after stripping
     * formatting characters) and passes Luhn checksum validation. The original
     * formatted value is preserved for display purposes.
     *
     * @param string $value The GTIN-8 string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GTIN-8 length is not exactly 8 digits
     *                                 or when Luhn validation fails
     *
     * @return self a validated GTIN-8 instance
     */
    public static function createFromString(string $value): self
    {
        return self::createWithValidation($value, 'GTIN-8', 8);
    }
}
