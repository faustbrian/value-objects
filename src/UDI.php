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

use function in_array;
use function mb_strlen;
use function throw_unless;

/**
 * Value object representing a Unique Device Identifier (UDI) barcode.
 *
 * UDI is a barcode identifier used in healthcare to uniquely identify medical
 * devices. Supports standard barcode formats (8, 12, 13, or 14 digits) with
 * Luhn algorithm validation to ensure barcode integrity.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final class UDI extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new UDI value object.
     *
     * @param string $value UDI barcode string, which may contain formatting characters
     *                      such as hyphens or spaces that are preserved for display
     */
    public function __construct(
        public readonly string $value,
    ) {}

    /**
     * Convert the UDI to its string representation.
     *
     * @return string The UDI barcode value including any formatting characters
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a UDI instance from a string value.
     *
     * Validates that the barcode has a valid length (8, 12, 13, or 14 digits)
     * and passes Luhn algorithm verification. Formatting characters (hyphens,
     * en-dashes, spaces) are stripped for validation but the original formatted
     * value is preserved in the instance.
     *
     * @param string $value The UDI barcode string to validate, may contain formatting
     *
     * @throws InvalidBarcodeException when the value has invalid length or fails Luhn validation
     *
     * @return self A validated UDI instance
     */
    public static function createFromString(string $value): self
    {
        /** @var string $valueStripped */
        $valueStripped = Str::replace(['‐', '-', ' '], '', $value);
        $valueStrippedLength = mb_strlen($valueStripped);

        throw_unless(in_array($valueStrippedLength, [8, 12, 13, 14], true), InvalidBarcodeException::invalid('UDI', $value));

        if (Luhn::check($valueStripped, $valueStrippedLength)) {
            return new self($value);
        }

        throw InvalidBarcodeException::invalid('UDI', $value);
    }

    /**
     * Compare this UDI with another for equality.
     *
     * @param  self $other The UDI to compare against
     * @return bool True if both UDIs have identical values
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the UDI barcode as a string.
     *
     * @return string The UDI barcode value including any formatting characters
     */
    public function toString(): string
    {
        return $this->value;
    }
}
