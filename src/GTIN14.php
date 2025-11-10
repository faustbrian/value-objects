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
 * Global Trade Item Number (GTIN-14) for logistics and shipping units.
 *
 * GTIN-14 is a 14-digit GS1 identifier used for trade items at various packaging
 * levels above the consumer unit. Primarily used on outer cartons and shipping
 * containers in distribution and logistics. The first digit (packaging indicator)
 * denotes the packaging level, followed by a GTIN-13 or GTIN-12 with padding,
 * and a check digit validated using the Luhn algorithm.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/gtin GS1 GTIN Documentation
 *
 * @psalm-immutable
 */
final class GTIN14 extends AbstractBarcode
{
    /**
     * Create a GTIN-14 from a string value with validation.
     *
     * Validates that the provided value is exactly 14 digits (after stripping
     * formatting characters) and passes Luhn checksum validation. The original
     * formatted value is preserved for display purposes.
     *
     * @param string $value The GTIN-14 string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GTIN-14 length is not exactly 14 digits
     *                                 or when Luhn validation fails
     *
     * @return self a validated GTIN-14 instance
     */
    public static function createFromString(string $value): self
    {
        return self::createWithValidation($value, 'GTIN-14', 14);
    }
}
