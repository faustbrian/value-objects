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

/**
 * Global Service Relation Number (GSRN) for identifying service relationships.
 *
 * GSRN is a GS1 standard 18-digit identifier used to identify the relationship
 * between a service provider and recipient. Commonly used in healthcare, financial
 * services, and customer loyalty programs to track service delivery and maintain
 * customer records. The full 18-digit value is validated using the Luhn algorithm.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @see https://www.gs1.org/standards/id-keys/gsrn GS1 GSRN Documentation
 *
 * @psalm-immutable
 */
final class GSRN extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new GSRN instance.
     *
     * @param string $value The GSRN string value, stored with original formatting
     *                      (hyphens, spaces) intact for display purposes. Must be
     *                      exactly 18 digits after stripping formatting characters.
     */
    public function __construct(
        public readonly string $value,
    ) {}

    /**
     * Convert the GSRN to its string representation.
     *
     * @return string the GSRN value as-is, including any formatting characters
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a GSRN from a string value with validation.
     *
     * Validates that the GSRN is exactly 18 digits and passes Luhn checksum
     * validation according to GS1 standards. Formatting characters (hyphens,
     * spaces) are stripped for validation but preserved in the stored value.
     *
     * @param string $value The GSRN string to validate. May contain formatting
     *                      characters (hyphens, spaces) which are stripped for
     *                      validation but preserved in the stored value.
     *
     * @throws InvalidBarcodeException when the GSRN is not exactly 18 digits
     *                                 or when Luhn validation fails
     *
     * @return self a validated GSRN instance
     */
    public static function createFromString(string $value): self
    {
        /** @var string $valueStripped */
        $valueStripped = Str::replace(['‐', '-', ' '], '', $value);

        if (Luhn::check($valueStripped, 18)) {
            return new self($value);
        }

        throw InvalidBarcodeException::invalid('GSRN', $value);
    }

    /**
     * Compare this GSRN with another for equality.
     *
     * @param  self $other the GSRN to compare against
     * @return bool true if the GSRN values are identical, false otherwise
     */
    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the GSRN value as a string.
     *
     * @return string the GSRN value as-is, including any formatting characters
     */
    public function toString(): string
    {
        return $this->value;
    }
}
