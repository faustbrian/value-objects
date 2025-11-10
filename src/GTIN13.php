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
 * Global Trade Item Number (GTIN-13) for product identification worldwide.
 *
 * GTIN-13 is a 13-digit GS1 identifier used globally for retail products. Also
 * known as EAN-13 (European Article Number), it is the most common barcode format
 * for products worldwide, particularly in Europe and internationally outside North
 * America. The 13 digits include a GS1 prefix, company prefix, item reference,
 * and check digit validated using the Luhn algorithm.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/gtin GS1 GTIN Documentation
 *
 * @psalm-immutable
 */
final class GTIN13 extends AbstractBarcode
{
    /**
     * Create a GTIN-13 from a string value with validation.
     *
     * Validates that the provided value is exactly 13 digits (after stripping
     * formatting characters) and passes Luhn checksum validation. The original
     * formatted value is preserved for display purposes.
     *
     * @param string $value The GTIN-13 string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GTIN-13 length is not exactly 13 digits
     *                                 or when Luhn validation fails
     *
     * @return self a validated GTIN-13 instance
     */
    public static function createFromString(string $value): self
    {
        return self::createWithValidation($value, 'GTIN-13', 13);
    }
}
