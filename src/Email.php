<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects;

use Cline\Data\Core\AbstractDataTransferObject;
use Cline\ValueObjects\Exceptions\Validation\InvalidEmailException;
use Override;
use Stringable;

use const FILTER_VALIDATE_EMAIL;

use function filter_var;
use function throw_if;

/**
 * Represents a validated email address value object.
 *
 * Immutable value object that encapsulates an email address with built-in
 * validation using PHP's filter_var function. Ensures email addresses are
 * non-empty and conform to RFC 822 email format standards.
 *
 * @author Brian Faust <brian@cline.sh>
 *
 * @version 1.0.2
 *
 * @psalm-immutable
 */
final class Email extends AbstractDataTransferObject implements Stringable
{
    /**
     * Create a new email value object.
     *
     * Validates that the email address is non-empty during construction.
     * Additional format validation is performed in the createFromString factory method.
     *
     * @param string $email Valid email address string
     *
     * @throws InvalidEmailException When the email address is empty
     */
    public function __construct(
        public readonly string $email,
    ) {
        throw_if($email === '' || $email === '0', InvalidEmailException::invalid($email));
    }

    /**
     * Convert the email to its string representation.
     *
     * @return string Email address
     */
    #[Override()]
    public function __toString(): string
    {
        return $this->email;
    }

    /**
     * Create an email instance from a string with validation.
     *
     * Validates the email address format using PHP's FILTER_VALIDATE_EMAIL filter,
     * which checks for RFC 822 compliance including proper format for local part,
     *
     * @ symbol, and domain structure.
     *
     * @param string $email Email address string to validate and encapsulate
     *
     * @throws InvalidEmailException When the email format is invalid or fails validation
     *
     * @return self New immutable email instance
     */
    public static function createFromString(string $email): self
    {
        throw_if(filter_var($email, FILTER_VALIDATE_EMAIL) === false, InvalidEmailException::invalid($email));

        return new self($email);
    }

    /**
     * Determine if this email is equal to another email.
     *
     * Comparison is case-sensitive and based on exact string matching.
     *
     * @param  self $other Email instance to compare against
     * @return bool True if both emails are exactly equal
     */
    public function isEqualTo(self $other): bool
    {
        return $this->email === $other->email;
    }

    /**
     * Convert the email to its string representation.
     *
     * @return string Email address
     */
    public function toString(): string
    {
        return $this->email;
    }
}
