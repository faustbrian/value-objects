<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\ShippingMethodIdentifier;

dataset('valid_identifiers', [
    'FedEx Express' => ['FedEx.Express', 'FedEx', 'Express'],
    'DHL Standard' => ['DHL.Standard', 'DHL', 'Standard'],
    'UPS Next Day' => ['UPS.NextDay', 'UPS', 'NextDay'],
]);

describe('ShippingMethodIdentifier', function (): void {
    describe('Happy Paths', function (): void {
        test('parses carrier and service from valid identifiers', function (string $identifier, string $carrier, string $service): void {
            $value = ShippingMethodIdentifier::createFromString($identifier);

            expect($value)->toBeInstanceOf(ShippingMethodIdentifier::class);
            expect($value->getCarrier())->toBe($carrier);
            expect($value->getService())->toBe($service);
        })->with('valid_identifiers');

        test('parses FedEx Express identifier', function (): void {
            $value = ShippingMethodIdentifier::createFromString('FedEx.Express');

            expect($value->getCarrier())->toBe('FedEx');
            expect($value->getService())->toBe('Express');
        });
    });

    describe('Sad Paths', function (): void {
        test('rejects identifier without carrier', function (): void {
            ShippingMethodIdentifier::createFromString('.MissingCarrier');
        })->throws(InvalidArgumentException::class);

        test('rejects identifier without service', function (): void {
            ShippingMethodIdentifier::createFromString('OnlyCarrier.');
        })->throws(InvalidArgumentException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles identifiers with multiple dots', function (): void {
            $value = ShippingMethodIdentifier::createFromString('Carrier.Service.Extra');

            expect($value->getCarrier())->toBe('Carrier');
            expect($value->getService())->toContain('Service');
        });

        test('handles identifiers with spaces in parts', function (): void {
            $value = ShippingMethodIdentifier::createFromString('FedEx.Next Day Air');

            expect($value->getCarrier())->toBe('FedEx');
            expect($value->getService())->toBe('Next Day Air');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
