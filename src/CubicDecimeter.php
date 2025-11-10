<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\InvalidDimensionsException;
use Illuminate\Support\Number;
use Override;
use Stringable;

use function throw_if;

/**
 * Represents a volume measurement in cubic decimeters (dm³).
 *
 * Immutable value object that encapsulates volumetric dimensions and provides
 * conversion methods between different metric units. Supports creation from
 * meter, decimeter, and centimeter measurements with configurable decimal precision.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class CubicDecimeter extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new cubic decimeter volume instance.
     *
     * @param float $volume   Total calculated volume in cubic decimeters (length × width × height)
     * @param float $length   Length dimension in decimeters used in the volume calculation
     * @param float $width    Width dimension in decimeters used in the volume calculation
     * @param float $height   Height dimension in decimeters used in the volume calculation
     * @param int   $decimals Number of decimal places to use when formatting the volume for display
     */
    public function __construct(
        public readonly float $volume,
        public readonly float $length,
        public readonly float $width,
        public readonly float $height,
        public readonly int $decimals,
    ) {}

    /**
     * Convert the volume to a string representation.
     *
     * @return string Formatted volume value with configured decimal precision
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a volume instance from meter dimensions.
     *
     * Converts meter measurements to decimeters (×10) before calculating volume.
     *
     * @param float    $length   Length in meters
     * @param float    $width    Width in meters
     * @param float    $height   Height in meters
     * @param null|int $decimals Number of decimal places for formatting (defaults to 0)
     *
     * @throws InvalidDimensionsException When any dimension is zero or empty
     *
     * @return self New cubic decimeter instance with converted dimensions
     */
    public static function createFromMeter(float $length, float $width, float $height, ?int $decimals = null): self
    {
        $length *= 10;
        $width *= 10;
        $height *= 10;

        return self::createFromDecimeter($length, $width, $height, $decimals ?? 0);
    }

    /**
     * Create a volume instance from a meter dimensions array.
     *
     * @param array{length: float, width: float, height: float} $dimensions Associative array containing length, width, and height in meters
     * @param null|int                                          $decimals   Number of decimal places for formatting (defaults to 0)
     *
     * @throws InvalidDimensionsException When any dimension is zero or empty
     *
     * @return self New cubic decimeter instance
     */
    public static function createFromMeterArray(array $dimensions, ?int $decimals = null): self
    {
        return self::createFromMeter($dimensions['length'], $dimensions['width'], $dimensions['height'], $decimals);
    }

    /**
     * Create a volume instance from decimeter dimensions.
     *
     * @param float    $length   Length in decimeters
     * @param float    $width    Width in decimeters
     * @param float    $height   Height in decimeters
     * @param null|int $decimals Number of decimal places for formatting (defaults to 0)
     *
     * @throws InvalidDimensionsException When any dimension is zero or empty
     *
     * @return self New cubic decimeter instance
     */
    public static function createFromDecimeter(float $length, float $width, float $height, ?int $decimals = null): self
    {
        throw_if(empty($width) || empty($height) || empty($length), InvalidDimensionsException::create($length, $width, $height));

        return new self($length * $width * $height, $length, $width, $height, $decimals ?? 0);
    }

    /**
     * Create a volume instance from a decimeter dimensions array.
     *
     * @param array{length: float, width: float, height: float} $dimensions Associative array containing length, width, and height in decimeters
     * @param null|int                                          $decimals   Number of decimal places for formatting (defaults to 0)
     *
     * @throws InvalidDimensionsException When any dimension is zero or empty
     *
     * @return self New cubic decimeter instance
     */
    public static function createFromDecimeterArray(array $dimensions, ?int $decimals = null): self
    {
        return self::createFromDecimeter($dimensions['length'], $dimensions['width'], $dimensions['height'], $decimals);
    }

    /**
     * Create a volume instance from centimeter dimensions.
     *
     * Converts centimeter measurements to decimeters (÷10) before calculating volume.
     *
     * @param float    $length   Length in centimeters
     * @param float    $width    Width in centimeters
     * @param float    $height   Height in centimeters
     * @param null|int $decimals Number of decimal places for formatting (defaults to 0)
     *
     * @throws InvalidDimensionsException When any dimension is zero or empty
     *
     * @return self New cubic decimeter instance with converted dimensions
     */
    public static function createFromCentimeter(float $length, float $width, float $height, ?int $decimals = null): self
    {
        $length /= 10;
        $width /= 10;
        $height /= 10;

        return self::createFromDecimeter($length, $width, $height, $decimals ?? 0);
    }

    /**
     * Create a volume instance from a centimeter dimensions array.
     *
     * @param array{length: float, width: float, height: float} $dimensions Associative array containing length, width, and height in centimeters
     * @param null|int                                          $decimals   Number of decimal places for formatting (defaults to 0)
     *
     * @throws InvalidDimensionsException When any dimension is zero or empty
     *
     * @return self New cubic decimeter instance
     */
    public static function createFromCentimeterArray(array $dimensions, ?int $decimals = null): self
    {
        return self::createFromCentimeter($dimensions['length'], $dimensions['width'], $dimensions['height'], $decimals);
    }

    /**
     * Get the calculated volume in cubic decimeters.
     *
     * @return float Total volume in dm³
     */
    public function getVolume(): float
    {
        return $this->volume;
    }

    /**
     * Get the length dimension.
     *
     * @return float Length in decimeters
     */
    public function getLength(): float
    {
        return $this->length;
    }

    /**
     * Get the width dimension.
     *
     * @return float Width in decimeters
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Get the height dimension.
     *
     * @return float Height in decimeters
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * Determine if this volume is equal to another volume.
     *
     * Comparison is based solely on the calculated volume value, not individual dimensions.
     *
     * @param  self $other Volume instance to compare against
     * @return bool True if both volumes are exactly equal
     */
    public function isEqualTo(self $other): bool
    {
        return $this->volume === $other->volume;
    }

    /**
     * Convert the volume to a formatted string.
     *
     * @return string Formatted volume value using Laravel's Number helper with configured decimal precision
     */
    public function toString(): string
    {
        return (string) Number::format($this->volume, $this->decimals);
    }
}
