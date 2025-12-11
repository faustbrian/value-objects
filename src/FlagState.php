<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use InvalidArgumentException;
use Stringable;

use function in_array;
use function mb_strtolower;
use function sprintf;
use function throw_unless;

/**
 * Represents a feature flag state with three possible values: pending, active, or inactive.
 *
 * This value object enforces type safety and validation for feature flag states,
 * ensuring only valid states can be represented. All state comparisons are
 * case-insensitive and normalized to lowercase.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @psalm-immutable
 */
final readonly class FlagState implements Stringable
{
    private const array VALID_STATES = ['pending', 'active', 'inactive'];

    /**
     * Create a new flag state instance.
     *
     * @param string $value The normalized (lowercase) flag state value. Must be one
     *                      of: 'pending', 'active', or 'inactive'. Private constructor
     *                      ensures creation only through named constructors or fromString().
     */
    private function __construct(
        private string $value,
    ) {}

    /**
     * Convert the flag state to its string representation.
     *
     * @return string the normalized flag state value
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create a flag state from a string value with validation.
     *
     * The input string is normalized to lowercase before validation, making
     * state comparison case-insensitive. This allows inputs like "ACTIVE",
     * "Active", or "active" to all be accepted and normalized.
     *
     * @param string $value The flag state string to parse. Case-insensitive, will be
     *                      normalized to lowercase. Must match one of the valid states.
     *
     * @throws InvalidArgumentException when the provided value is not a valid flag state
     *
     * @return self a validated flag state instance
     */
    public static function fromString(string $value): self
    {
        $normalised = mb_strtolower($value);

        throw_unless(in_array($normalised, self::VALID_STATES, true), InvalidArgumentException::class, sprintf("Unsupported flag state '%s'.", $value));

        return new self($normalised);
    }

    /**
     * Create a pending flag state.
     *
     * Represents a feature flag that has been created but not yet activated.
     * Typically used for flags that are being prepared or tested before deployment.
     *
     * @return self a flag state with value 'pending'
     */
    public static function pending(): self
    {
        return new self('pending');
    }

    /**
     * Create an active flag state.
     *
     * Represents a feature flag that is currently enabled and operational.
     * This is the state used to enable features in production environments.
     *
     * @return self a flag state with value 'active'
     */
    public static function active(): self
    {
        return new self('active');
    }

    /**
     * Create an inactive flag state.
     *
     * Represents a feature flag that has been explicitly disabled. Differs
     * from 'pending' in that it indicates a feature that was previously active
     * or deliberately turned off, rather than one awaiting activation.
     *
     * @return self a flag state with value 'inactive'
     */
    public static function inactive(): self
    {
        return new self('inactive');
    }

    /**
     * Check if this flag state is active.
     *
     * @return bool true if the state is 'active', false otherwise
     */
    public function isActive(): bool
    {
        return $this->value === 'active';
    }

    /**
     * Check if this flag can be activated.
     *
     * Returns true for both 'pending' and 'inactive' states, allowing
     * transitions to the active state. Returns false when already active.
     *
     * @return bool true if the flag is not currently active and can be activated
     */
    public function canBeActivated(): bool
    {
        return $this->value !== 'active';
    }

    /**
     * Get the raw flag state value.
     *
     * @return string the normalized flag state value
     */
    public function value(): string
    {
        return $this->value;
    }
}
