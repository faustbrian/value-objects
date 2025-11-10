<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Override;
use Stringable;

use function sprintf;
use function throw_if;

/**
 * Value object representing a shipping method identifier.
 *
 * Encapsulates a carrier and service identifier in the format "carrier.service",
 * providing structured access to shipping method components. Used to uniquely
 * identify shipping methods across different carriers and their service levels.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final class ShippingMethodIdentifier extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new shipping method identifier.
     *
     * @param string      $source  Original identifier string in "carrier.service" format
     * @param string      $carrier Carrier identifier (e.g., "dhl", "fedex", "ups")
     * @param null|string $service Service level identifier (e.g., "express", "ground", "overnight")
     */
    public function __construct(
        public readonly string $source,
        public readonly string $carrier,
        public readonly ?string $service,
    ) {}

    /**
     * Convert the identifier to its string representation.
     *
     * @return string The identifier in "carrier.service" format
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Create a shipping method identifier from a string.
     *
     * Parses a shipping method string in the format "carrier.service" and
     * validates that both components are present. The string must contain
     * exactly one period separating the carrier and service components.
     *
     * @param string $value The identifier string in "carrier.service" format
     *
     * @throws InvalidArgumentException when the value is not in the expected format
     *
     * @return self A parsed shipping method identifier instance
     */
    public static function createFromString(string $value): self
    {
        $carrier = Str::before(subject: $value, search: '.') ?: null;

        throw_if($carrier === null, InvalidArgumentException::class, 'Invalid service identifier: '.$value);

        $service = Str::after(subject: $value, search: '.') ?: null;

        throw_if($service === null, InvalidArgumentException::class, 'Invalid service identifier: '.$value);

        return new self(source: $value, carrier: $carrier, service: $service);
    }

    /**
     * Get the original source identifier string.
     *
     * @return string The original identifier in "carrier.service" format
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Get the carrier identifier.
     *
     * @return string The carrier identifier
     */
    public function getCarrier(): string
    {
        return $this->carrier;
    }

    /**
     * Get the service level identifier.
     *
     * @return null|string The service level identifier
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * Compare this identifier with another for equality.
     *
     * @param  self $other The identifier to compare against
     * @return bool True if both carrier and service match
     */
    public function isEqualTo(self $other): bool
    {
        return $this->carrier === $other->carrier && $this->service === $other->service;
    }

    /**
     * Get the identifier as a string.
     *
     * @return string The identifier in "carrier.service" format
     */
    public function toString(): string
    {
        return sprintf('%s.%s', $this->carrier, $this->service);
    }
}
