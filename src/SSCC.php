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
 * Value object representing a Serial Shipping Container Code (SSCC).
 *
 * SSCC is an 18-digit GS1 barcode identifier used to identify logistics units
 * in supply chain management. Each SSCC uniquely identifies a shipping container
 * or pallet and enables tracking throughout the distribution process.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final class SSCC extends AbstractBarcode
{
    /**
     * Create an SSCC instance from a string value.
     *
     * Validates that the provided value is a valid 18-digit SSCC barcode
     * using Luhn algorithm verification. The input may contain formatting
     * characters which are stripped during validation.
     *
     * @param string $value The SSCC barcode string to validate (must be 18 digits)
     *
     * @throws InvalidBarcodeException when the value is not a valid 18-digit SSCC
     *
     * @return self A validated SSCC instance
     */
    public static function createFromString(string $value): self
    {
        return self::createWithValidation($value, 'SSCC', 18);
    }
}
