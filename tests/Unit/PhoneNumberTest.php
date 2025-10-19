<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Brick\PhoneNumber\PhoneNumberException;
use Brick\PhoneNumber\PhoneNumberParseException;
use Cline\ValueObjects\PhoneNumber;

describe('PhoneNumber', function (): void {
    describe('Happy Paths', function (): void {
        describe('Creation and Properties', function (): void {
            test('creates from valid international phone number', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->toString())->toBe($validPhoneNumber);
                expect($phoneNumber->countryCode)->toBe('358');
                expect($phoneNumber->geographicalAreaCode)->toBeNull();
                expect($phoneNumber->nationalNumber)->toBe('10800515');
                expect($phoneNumber->regionCode)->toBe('FI');
                expect($phoneNumber->isPossible)->toBeTrue();
                expect($phoneNumber->isValid)->toBeTrue();
                expect($phoneNumber->numberType)->toBe(9);
            });

            test('returns correct string representation', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->toString())->toBe($validPhoneNumber);
            });

            test('returns correct country code', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->countryCode)->toBe('358');
            });

            test('returns correct geographical area code', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->geographicalAreaCode)->toBeNull();
            });

            test('returns correct national number', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->nationalNumber)->toBe('10800515');
            });

            test('returns correct region code', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->regionCode)->toBe('FI');
            });

            test('checks if phone number is possible', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->isPossible)->toBeTrue();
            });

            test('checks if phone number is valid', function (): void {
                $validPhoneNumber = '+35810800515';
                $phoneNumber = PhoneNumber::createFromString($validPhoneNumber);

                expect($phoneNumber->isValid)->toBeTrue();
            });
        });

        describe('Parsing', function (): void {
            test('parses non-standard format numbers with region code', function (): void {
                $phoneNumber = PhoneNumber::createFromString('(0)10 800 515', 'FI');

                expect($phoneNumber->toString())->toBe('+35810800515');
                expect($phoneNumber->countryCode)->toBe('358');
                expect($phoneNumber->geographicalAreaCode)->toBeNull();
                expect($phoneNumber->nationalNumber)->toBe('10800515');
                expect($phoneNumber->regionCode)->toBe('FI');
                expect($phoneNumber->isPossible)->toBeTrue();
                expect($phoneNumber->isValid)->toBeTrue();
            });
        });

        describe('Serialization', function (): void {
            test('serializes to JSON correctly', function (): void {
                $phoneNumber = PhoneNumber::createFromString('0123000000', 'FR');

                expect(json_encode($phoneNumber))->toBe('{"phoneNumber":"+33123000000"}');
            });
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid phone number', function (): void {
            PhoneNumber::createFromString('invalid-phone-number');
        })->throws(PhoneNumberParseException::class);

        test('throws exception for invalid region code', function (): void {
            PhoneNumber::createFromString('ZZ');
        })->throws(PhoneNumberException::class);
    });

    describe('Edge Cases', function (): void {
        test('handles different international formats', function (): void {
            $us = PhoneNumber::createFromString('+12025550173');
            expect($us->regionCode)->toBe('US');
            expect($us->countryCode)->toBe('1');

            $uk = PhoneNumber::createFromString('+442071838750');
            expect($uk->regionCode)->toBe('GB');
            expect($uk->countryCode)->toBe('44');
        });

        test('handles national format with region code', function (): void {
            $phoneNumber = PhoneNumber::createFromString('0701234567', 'SE');

            expect($phoneNumber->regionCode)->toBe('SE');
            expect($phoneNumber->isValid)->toBeTrue();
        });

        test('handles numbers with various formatting', function (): void {
            $formatted = PhoneNumber::createFromString('+46 70 123 45 67');
            expect($formatted->toString())->toBe('+46701234567');

            $dashes = PhoneNumber::createFromString('+46-70-123-45-67');
            expect($dashes->toString())->toBe('+46701234567');
        });
    });

    describe('Regressions', function (): void {
        // Add when bugs are discovered
    });
});
