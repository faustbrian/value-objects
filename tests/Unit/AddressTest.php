<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\Intl\Enum\CountryCode;
use Cline\ValueObjects\Address;

function createSampleAddress(array $overrides = []): Address
{
    return new Address(
        countryCode: $overrides['countryCode'] ?? CountryCode::US,
        administrativeArea: $overrides['administrativeArea'] ?? 'NY',
        locality: $overrides['locality'] ?? 'Anytown',
        dependentLocality: $overrides['dependentLocality'] ?? null,
        postalCode: $overrides['postalCode'] ?? '12345',
        sortingCode: $overrides['sortingCode'] ?? null,
        addressLine1: $overrides['addressLine1'] ?? '123 Main St',
        addressLine2: $overrides['addressLine2'] ?? 'Apt 4',
        addressLine3: $overrides['addressLine3'] ?? null,
        fullName: $overrides['fullName'] ?? null,
        givenName: $overrides['givenName'] ?? null,
        additionalName: $overrides['additionalName'] ?? null,
        familyName: $overrides['familyName'] ?? null,
        organization: $overrides['organization'] ?? null,
        locale: $overrides['locale'] ?? null,
        phoneNumber: $overrides['phoneNumber'] ?? null,
        latitude: $overrides['latitude'] ?? null,
        longitude: $overrides['longitude'] ?? null,
    );
}

describe('Address', function (): void {
    describe('Happy Paths', function (): void {
        test('creates address with all required fields', function (): void {
            $address = createSampleAddress();

            expect($address)->toBeInstanceOf(Address::class);
            expect($address->countryCode)->toEqual(CountryCode::US);
            expect($address->administrativeArea)->toBe('NY');
            expect($address->locality)->toBe('Anytown');
            expect($address->postalCode)->toBe('12345');
            expect($address->addressLine1)->toBe('123 Main St');
            expect($address->addressLine2)->toBe('Apt 4');
        });

        test('creates address with minimal fields', function (): void {
            $address = new Address(
                countryCode: CountryCode::SE,
                administrativeArea: null,
                locality: 'Stockholm',
                dependentLocality: null,
                postalCode: null,
                sortingCode: null,
                addressLine1: null,
                addressLine2: null,
                addressLine3: null,
                fullName: null,
                givenName: null,
                additionalName: null,
                familyName: null,
                organization: null,
                locale: null,
                phoneNumber: null,
                latitude: null,
                longitude: null,
            );

            expect($address)->toBeInstanceOf(Address::class);
            expect($address->locality)->toBe('Stockholm');
        });

        test('creates private address', function (): void {
            $address = createSampleAddress([
                'givenName' => 'John',
                'familyName' => 'Doe',
                'organization' => null,
            ]);

            expect($address->isPrivateAddress())->toBeTrue();
            expect($address->isCompanyAddress())->toBeFalse();
        });

        test('creates company address', function (): void {
            $address = createSampleAddress([
                'organization' => 'Acme Corp',
            ]);

            expect($address->isCompanyAddress())->toBeTrue();
            expect($address->isPrivateAddress())->toBeFalse();
        });

        test('compares identical addresses as equal', function (): void {
            $address1 = createSampleAddress();
            $address2 = createSampleAddress();

            expect($address1->isEqualTo($address2))->toBeTrue();
        });

        test('creates address with coordinates', function (): void {
            $address = createSampleAddress([
                'latitude' => 59.329_3,
                'longitude' => 18.068_6,
            ]);

            expect($address->latitude)->toBe(59.329_3);
            expect($address->longitude)->toBe(18.068_6);
        });
    });

    describe('Sad Paths', function (): void {
        test('compares different addresses as not equal', function (): void {
            $address1 = createSampleAddress();
            $address2 = createSampleAddress(['locality' => 'Different City']);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });

        test('detects inequality when country differs', function (): void {
            $address1 = createSampleAddress(['countryCode' => CountryCode::US]);
            $address2 = createSampleAddress(['countryCode' => CountryCode::SE]);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });

        test('detects inequality when postal code differs', function (): void {
            $address1 = createSampleAddress(['postalCode' => '12345']);
            $address2 = createSampleAddress(['postalCode' => '54321']);

            expect($address1->isEqualTo($address2))->toBeFalse();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles address with all optional fields null', function (): void {
            $address = new Address(
                countryCode: null,
                administrativeArea: null,
                locality: 'City',
                dependentLocality: null,
                postalCode: null,
                sortingCode: null,
                addressLine1: null,
                addressLine2: null,
                addressLine3: null,
                fullName: null,
                givenName: null,
                additionalName: null,
                familyName: null,
                organization: null,
                locale: null,
                phoneNumber: null,
                latitude: null,
                longitude: null,
            );

            expect($address->locality)->toBe('City');
            expect($address->countryCode)->toBeNull();
        });

        test('handles address with all three address lines', function (): void {
            $address = createSampleAddress([
                'addressLine1' => '123 Main St',
                'addressLine2' => 'Building B',
                'addressLine3' => 'Floor 3',
            ]);

            expect($address->addressLine1)->toBe('123 Main St');
            expect($address->addressLine2)->toBe('Building B');
            expect($address->addressLine3)->toBe('Floor 3');
        });

        test('handles address with full name components', function (): void {
            $address = createSampleAddress([
                'givenName' => 'John',
                'additionalName' => 'Michael',
                'familyName' => 'Doe',
                'fullName' => 'Dr. John Michael Doe Jr.',
            ]);

            expect($address->givenName)->toBe('John');
            expect($address->additionalName)->toBe('Michael');
            expect($address->familyName)->toBe('Doe');
            expect($address->fullName)->toBe('Dr. John Michael Doe Jr.');
        });

        test('handles address with dependent locality', function (): void {
            $address = createSampleAddress([
                'dependentLocality' => 'Whaley, Langwith',
                'locality' => 'Bolsover',
            ]);

            expect($address->dependentLocality)->toBe('Whaley, Langwith');
            expect($address->locality)->toBe('Bolsover');
        });

        test('handles address with sorting code', function (): void {
            $address = createSampleAddress([
                'sortingCode' => 'CEDEX 16',
            ]);

            expect($address->sortingCode)->toBe('CEDEX 16');
        });

        test('handles address equality with all null fields', function (): void {
            $address1 = new Address(
                countryCode: null,
                administrativeArea: null,
                locality: 'City',
                dependentLocality: null,
                postalCode: null,
                sortingCode: null,
                addressLine1: null,
                addressLine2: null,
                addressLine3: null,
                fullName: null,
                givenName: null,
                additionalName: null,
                familyName: null,
                organization: null,
                locale: null,
                phoneNumber: null,
                latitude: null,
                longitude: null,
            );
            $address2 = new Address(
                countryCode: null,
                administrativeArea: null,
                locality: 'City',
                dependentLocality: null,
                postalCode: null,
                sortingCode: null,
                addressLine1: null,
                addressLine2: null,
                addressLine3: null,
                fullName: null,
                givenName: null,
                additionalName: null,
                familyName: null,
                organization: null,
                locale: null,
                phoneNumber: null,
                latitude: null,
                longitude: null,
            );

            expect($address1->isEqualTo($address2))->toBeTrue();
        });

        test('handles address with phone number', function (): void {
            $address = createSampleAddress([
                'phoneNumber' => '+1-555-123-4567',
            ]);

            expect($address->phoneNumber)->toBe('+1-555-123-4567');
        });

        test('handles address with locale', function (): void {
            $address = createSampleAddress([
                'locale' => 'en_US',
            ]);

            expect($address->locale)->toBe('en_US');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
