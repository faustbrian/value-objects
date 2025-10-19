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
use function mb_substr;
use function throw_if;

/**
 * Global Document Type Identifier (GDTI) for uniquely identifying document types.
 *
 * GDTI is a GS1 standard used to identify document types such as invoices,
 * purchase orders, shipping notices, and other business documents. Consists
 * of a 13-digit base identifier plus optional serial component (up to 17 chars).
 * The first 13 digits are validated using the Luhn algorithm.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/gdti GS1 GDTI Documentation
 *
 * @psalm-immutable
 */
final class GDTI extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new GDTI instance.
     *
     * @param string $value The GDTI string value, stored with original formatting
     *                      (hyphens, spaces) intact for display purposes. Must be
     *                      between 13 and 30 characters after stripping formatting.
     */
    public function __construct(
        public readonly string $value,
    ) {}

    /**
     * Convert the GDTI to its string representation.
     *
     * @return string the GDTI value as-is, including any formatting characters
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a GDTI from a string value with validation.
     *
     * Validates the GDTI structure and checksum according to GS1 standards.
     * The value must have at least 13 characters (the base GDTI) and no more
     * than 30 characters total (including optional serial component). The first
     * 13 digits are validated using the Luhn algorithm.
     *
     * @param string $value The GDTI string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GDTI length is invalid (< 13 or > 30 characters)
     *                                 or when Luhn validation fails on the first 13 digits
     *
     * @return self a validated GDTI instance
     */
    public static function createFromString(string $value): self
    {
        throw_if(mb_strlen($value) < 13, InvalidBarcodeException::invalid('GDTI', $value));

        /** @var string $valueStripped */
        $valueStripped = Str::replace(['‐', '-', ' '], '', $value);

        throw_if(mb_strlen($valueStripped) > 30, InvalidBarcodeException::invalid('GDTI', $value));

        if (Luhn::check(mb_substr($valueStripped, 0, 13), 13)) {
            return new self($value);
        }

        throw InvalidBarcodeException::invalid('GDTI', $value);
    }

    /**
     * Compare this GDTI with another for equality.
     *
     * @param  self $other the GDTI to compare against
     * @return bool true if the GDTI values are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the GDTI value as a string.
     *
     * @return string the GDTI value as-is, including any formatting characters
     */
    public function toString(): string
    {
        return $this->value;
    }
}
