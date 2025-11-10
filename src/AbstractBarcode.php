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

use function mb_strlen;
use function throw_if;

/**
 * Abstract base class for GS1 barcode value objects.
 *
 * Provides shared validation and formatting logic for barcode types such as
 * GTIN, EAN, UPC, and others. Uses Luhn algorithm validation to ensure
 * barcode integrity according to GS1 standards.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
abstract class AbstractBarcode extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new barcode value object.
     *
     * @param string $value The barcode string value, which may contain formatting
     *                      characters (hyphens, spaces) that will be stripped during
     *                      validation. The raw value is stored as-is for display purposes.
     */
    public function __construct(
        public readonly string $value,
    ) {}

    /**
     * Convert the barcode to its string representation.
     *
     * @return string the barcode value as-is, including any formatting characters
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Compare this barcode with another for equality.
     *
     * @param  self $other the barcode to compare against
     * @return bool true if the barcode values are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the barcode value as a string.
     *
     * @return string the barcode value as-is, including any formatting characters
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Create barcode from string with validation.
     *
     * Strips common formatting characters (hyphens, spaces) and validates
     * the barcode using Luhn algorithm. The original formatted value is
     * preserved in the created instance for display purposes.
     *
     * @param string $value  the barcode string to validate, may contain formatting characters
     * @param string $type   The barcode type name used in exception messages (e.g., "GTIN-13").
     * @param int    $length the expected length of the barcode after stripping formatting
     *
     * @throws InvalidBarcodeException when the barcode length is incorrect or Luhn validation fails
     *
     * @return static a validated barcode instance
     */
    protected static function createWithValidation(string $value, string $type, int $length): static
    {
        /** @var string $valueStripped */
        $valueStripped = Str::replace(['‐', '-', ' '], '', $value);

        throw_if(mb_strlen($valueStripped) !== $length, InvalidBarcodeException::invalid($type, $value));

        if (Luhn::check($valueStripped, $length)) {
            /** @phpstan-ignore-next-line new.static -- This is a factory method pattern where static() is guaranteed to be the correct subclass */
            return new static($value);
        }

        throw InvalidBarcodeException::invalid($type, $value);
    }
}
