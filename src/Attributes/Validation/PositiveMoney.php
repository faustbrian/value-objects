<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\ValueObjects\Attributes\Validation;

use Attribute;
use Cline\ValueObjects\Money as AppMoney;
use Override;
use Spatie\LaravelData\Attributes\Validation\CustomValidationAttribute;
use Spatie\LaravelData\Support\Validation\ValidationPath;

use function array_key_exists;
use function is_array;
use function is_numeric;

/**
 * Validates that a Money value object or serialized money array has a positive amount.
 *
 * This attribute ensures the money value is positive (amount_in_minor_units >= 0).
 * Supports both the application's Money value object and serialized array
 * representations with 'amount_in_minor_units' key. Useful for validating
 * payments, prices, or other monetary values that must not be negative.
 *
 * @author Brian Faust <brian@cline.sh>
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class PositiveMoney extends CustomValidationAttribute
{
    /**
     * Returns the validation rules for positive money validation.
     *
     * Implements a custom closure-based validation rule that checks:
     * - Money value objects: validates via getAmountInMinorUnits() method
     * - Array representations: validates via 'amount_in_minor_units' key
     * - Unknown formats: silently passes to avoid breaking validation chain
     *
     * @param  ValidationPath    $path The validation path context for the field
     * @return array<int, mixed> Array containing closure validation rule
     */
    #[Override()]
    public function getRules(ValidationPath $path): array
    {
        return [
            function (string $attribute, mixed $value, callable $fail): void {
                // Support Money value object instances
                if ($value instanceof AppMoney) {
                    if ($value->getAmountInMinorUnits() < 0) {
                        $fail('The :attribute amount must be positive.');
                    }

                    return;
                }

                // Support serialized money array shape with amount_in_minor_units key
                if (is_array($value) && array_key_exists('amount_in_minor_units', $value)) {
                    $amount = $value['amount_in_minor_units'];

                    if (is_numeric($amount) && (int) $amount < 0) {
                        $fail('The :attribute amount must be positive.');
                    }

                    return;
                }

                // Fallback: unknown format passes validation to avoid blocking valid data
            },
        ];
    }
}
