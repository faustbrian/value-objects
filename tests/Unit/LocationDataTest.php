<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Unit;

use Cline\Intl\Enum\CountryCode;
use Cline\ValueObjects\GeographicCoordinate;
use Cline\ValueObjects\Latitude;
use Cline\ValueObjects\LocationData;
use Cline\ValueObjects\Longitude;
use Cline\ValueObjects\PostalCode;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

use const PHP_FLOAT_EPSILON;

use function array_merge;

/**
 * @internal
 *
 * @author Brian Faust <brian@cline.sh>
 */
#[CoversClass(LocationData::class)]
#[Small()]
final class LocationDataTest extends TestCase
{
    #[Test()]
    #[TestDox('Creates location data from complete Google Maps result')]
    #[Group('happy-path')]
    public function creates_location_data_from_complete_google_maps_result(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 59.329_3,
                    'lng' => 18.068_6,
                ],
                'location_type' => 'ROOFTOP',
            ],
            'place_id' => 'ChIJywtkGTF2X0YRZnedZ9MnDag',
            'country_code' => 'SE',
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'SE',
                    'long_name' => 'Sweden',
                ],
                [
                    'types' => ['postal_code'],
                    'short_name' => '11122',
                    'long_name' => '11122',
                ],
                [
                    'types' => ['locality'],
                    'short_name' => 'Stockholm',
                    'long_name' => 'Stockholm',
                ],
                [
                    'types' => ['administrative_area_level_1'],
                    'short_name' => 'Stockholm County',
                    'long_name' => 'Stockholm County',
                ],
                [
                    'types' => ['administrative_area_level_2'],
                    'short_name' => 'Stockholm Municipality',
                    'long_name' => 'Stockholm Municipality',
                ],
            ],
            'street_address' => 'Drottninggatan 1',
            'formatted_address' => 'Drottninggatan 1, 111 22 Stockholm, Sweden',
            'administrative_area_level_1' => 'Stockholm County',
            'administrative_area_level_2' => 'Stockholm Municipality',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertInstanceOf(LocationData::class, $locationData);
        self::assertEqualsWithDelta(59.329_3, $locationData->getCoordinate()->getLatitude()->toValue(), PHP_FLOAT_EPSILON);
        self::assertEqualsWithDelta(18.068_6, $locationData->getCoordinate()->getLongitude()->toValue(), PHP_FLOAT_EPSILON);
        self::assertSame('ROOFTOP', $locationData->getCoordinate()->getLocationType());
        self::assertSame('ChIJywtkGTF2X0YRZnedZ9MnDag', $locationData->getCoordinate()->getPlaceId());
        self::assertSame(CountryCode::SE, $locationData->getCountryCode());
        self::assertSame('111 22', $locationData->getPostalCode()->toString());
        self::assertSame('Stockholm', $locationData->getLocality());
        self::assertSame('Drottninggatan 1', $locationData->getStreetAddress());
        self::assertSame('Drottninggatan 1, 111 22 Stockholm, Sweden', $locationData->getFormattedAddress());
        self::assertCount(2, $locationData->getAdministrativeLevels());
    }

    #[Test()]
    #[TestDox('Creates location data from minimal Google Maps result')]
    #[Group('happy-path')]
    public function creates_location_data_from_minimal_google_maps_result(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 40.712_8,
                    'lng' => -74.006_0,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'US',
                ],
                [
                    'types' => ['locality'],
                    'long_name' => 'New York',
                ],
            ],
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertInstanceOf(LocationData::class, $locationData);
        self::assertEqualsWithDelta(40.712_8, $locationData->getCoordinate()->getLatitude()->toValue(), PHP_FLOAT_EPSILON);
        self::assertSame(-74.006_0, $locationData->getCoordinate()->getLongitude()->toValue());
        self::assertNull($locationData->getCoordinate()->getLocationType());
        self::assertNull($locationData->getCoordinate()->getPlaceId());
        self::assertSame(CountryCode::US, $locationData->getCountryCode());
        self::assertSame('', $locationData->getPostalCode()->toString());
        self::assertSame('New York', $locationData->getLocality());
        self::assertNull($locationData->getStreetAddress());
        self::assertNull($locationData->getFormattedAddress());
        self::assertEmpty($locationData->getAdministrativeLevels());
    }

    #[Test()]
    #[TestDox('Creates location data through direct constructor with all fields')]
    #[Group('happy-path')]
    public function creates_location_data_through_direct_constructor_with_all_fields(): void
    {
        // Arrange
        $coordinate = new GeographicCoordinate(
            latitude: Latitude::createFromNumber(51.507_4),
            longitude: Longitude::createFromNumber(-0.127_8),
            locationType: 'ROOFTOP',
            placeId: 'ChIJdd4hrwug2EcRmSrV3Vo6llI',
        );
        $countryCode = CountryCode::GB;
        $postalCode = PostalCode::createFromString('SW1A 1AA', $countryCode->value);
        $locality = 'London';
        $streetAddress = '10 Downing Street';
        $administrativeLevels = [
            'administrative_area_level_1' => 'England',
            'administrative_area_level_2' => 'Greater London',
        ];
        $formattedAddress = '10 Downing St, Westminster, London SW1A 1AA, UK';

        // Act
        $locationData = new LocationData(
            coordinate: $coordinate,
            countryCode: $countryCode,
            postalCode: $postalCode,
            locality: $locality,
            streetAddress: $streetAddress,
            administrativeLevels: $administrativeLevels,
            formattedAddress: $formattedAddress,
        );

        // Assert
        self::assertInstanceOf(LocationData::class, $locationData);
        self::assertSame($coordinate, $locationData->getCoordinate());
        self::assertSame($countryCode, $locationData->getCountryCode());
        self::assertSame($postalCode, $locationData->getPostalCode());
        self::assertSame($locality, $locationData->getLocality());
        self::assertSame($streetAddress, $locationData->getStreetAddress());
        self::assertSame($administrativeLevels, $locationData->getAdministrativeLevels());
        self::assertSame($formattedAddress, $locationData->getFormattedAddress());
    }

    #[Test()]
    #[TestDox('Creates location data through direct constructor with minimal fields')]
    #[Group('happy-path')]
    public function creates_location_data_through_direct_constructor_with_minimal_fields(): void
    {
        // Arrange
        $coordinate = new GeographicCoordinate(
            latitude: Latitude::createFromNumber(48.856_6),
            longitude: Longitude::createFromNumber(2.352_2),
        );
        $countryCode = CountryCode::FR;
        $postalCode = PostalCode::createFromString('75001', $countryCode->value);
        $locality = 'Paris';

        // Act
        $locationData = new LocationData(
            coordinate: $coordinate,
            countryCode: $countryCode,
            postalCode: $postalCode,
            locality: $locality,
            streetAddress: null,
            administrativeLevels: null,
            formattedAddress: null,
        );

        // Assert
        self::assertInstanceOf(LocationData::class, $locationData);
        self::assertSame($coordinate, $locationData->getCoordinate());
        self::assertSame($countryCode, $locationData->getCountryCode());
        self::assertSame($postalCode, $locationData->getPostalCode());
        self::assertSame($locality, $locationData->getLocality());
        self::assertNull($locationData->getStreetAddress());
        self::assertNull($locationData->getAdministrativeLevels());
        self::assertNull($locationData->getFormattedAddress());
    }

    #[Test()]
    #[TestDox('Throws exception when country code is missing from Google Maps result')]
    #[Group('sad-path')]
    public function throws_exception_when_country_code_is_missing_from_google_maps_result(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 40.712_8,
                    'lng' => -74.006_0,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['locality'],
                    'long_name' => 'New York',
                ],
            ],
        ];

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Country code not found in Google Maps result');

        LocationData::createFromGoogleMapsResult($googleMapsResult);
    }

    #[Test()]
    #[TestDox('Throws exception when locality cannot be determined from any fallback')]
    #[Group('sad-path')]
    public function throws_exception_when_locality_cannot_be_determined_from_any_fallback(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 0.0,
                    'lng' => 0.0,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'US',
                ],
            ],
        ];

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Locality not found in Google Maps result');

        LocationData::createFromGoogleMapsResult($googleMapsResult);
    }

    #[Test()]
    #[TestDox('Handles empty postal code for locations without postal codes')]
    #[Group('edge-case')]
    public function handles_empty_postal_code_for_locations_without_postal_codes(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => -1.292_1,
                    'lng' => 36.821_9,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'KE',
                ],
                [
                    'types' => ['locality'],
                    'long_name' => 'Nairobi',
                ],
            ],
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertSame('', $locationData->getPostalCode()->toString());
        self::assertSame(CountryCode::KE, $locationData->getPostalCode()->getCountryCode());
    }

    #[Test()]
    #[TestDox('Uses postal town as locality fallback when locality is missing')]
    #[Group('edge-case')]
    public function uses_postal_town_as_locality_fallback_when_locality_is_missing(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 51.507_4,
                    'lng' => -0.127_8,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'GB',
                ],
            ],
            'postal_town' => 'London',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertSame('London', $locationData->getLocality());
    }

    #[Test()]
    #[TestDox('Uses sublocality level 1 as locality fallback')]
    #[Group('edge-case')]
    public function uses_sublocality_level1_as_locality_fallback(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 35.676_2,
                    'lng' => 139.650_3,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'JP',
                ],
            ],
            'sublocality_level_1' => 'Shibuya',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertSame('Shibuya', $locationData->getLocality());
    }

    #[Test()]
    #[TestDox('Uses administrative area level 1 as locality fallback')]
    #[Group('edge-case')]
    public function uses_administrative_area_level1_as_locality_fallback(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 37.774_9,
                    'lng' => -122.419_4,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'US',
                ],
            ],
            'administrative_area_level_1' => 'California',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertSame('California', $locationData->getLocality());
    }

    #[Test()]
    #[TestDox('Uses postal code as last resort locality fallback')]
    #[Group('edge-case')]
    public function uses_postal_code_as_last_resort_locality_fallback(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 40.712_8,
                    'lng' => -74.006_0,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'US',
                ],
            ],
            'postal_code' => '10001',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertEquals('10001', $locationData->getLocality());
    }

    #[Test()]
    #[TestDox('Handles locality as array by using first element')]
    #[Group('edge-case')]
    public function handles_locality_as_array_by_using_first_element(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 52.520_0,
                    'lng' => 13.405_0,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'DE',
                ],
            ],
            'locality' => ['Berlin', 'Berlin-Mitte'],
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertSame('Berlin', $locationData->getLocality());
    }

    #[Test()]
    #[TestDox('Extracts multiple administrative levels from Google Maps result')]
    #[Group('edge-case')]
    public function extracts_multiple_administrative_levels_from_google_maps_result(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 34.052_2,
                    'lng' => -118.243_7,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'US',
                ],
                [
                    'types' => ['locality'],
                    'long_name' => 'Los Angeles',
                ],
                [
                    'types' => ['administrative_area_level_1'],
                    'short_name' => 'CA',
                    'long_name' => 'California',
                ],
                [
                    'types' => ['administrative_area_level_2'],
                    'short_name' => 'LA County',
                    'long_name' => 'Los Angeles County',
                ],
                [
                    'types' => ['administrative_area_level_3'],
                    'short_name' => 'LA',
                    'long_name' => 'Los Angeles',
                ],
            ],
            'administrative_area_level_4' => 'District 4',
            'administrative_area_level_5' => 'Subdistrict 5',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        $administrativeLevels = $locationData->getAdministrativeLevels();
        self::assertCount(5, $administrativeLevels);
        self::assertSame('California', $administrativeLevels['administrative_area_level_1']);
        self::assertSame('Los Angeles County', $administrativeLevels['administrative_area_level_2']);
        self::assertSame('Los Angeles', $administrativeLevels['administrative_area_level_3']);
        self::assertSame('District 4', $administrativeLevels['administrative_area_level_4']);
        self::assertSame('Subdistrict 5', $administrativeLevels['administrative_area_level_5']);
    }

    #[Test()]
    #[TestDox('Handles null optional fields gracefully')]
    #[Group('edge-case')]
    public function handles_null_optional_fields_gracefully(): void
    {
        // Arrange
        $coordinate = new GeographicCoordinate(
            latitude: Latitude::createFromNumber(0.0),
            longitude: Longitude::createFromNumber(0.0),
        );
        $countryCode = CountryCode::US;
        $postalCode = PostalCode::createFromString('12345', $countryCode->value);
        $locality = 'Test Location';

        // Act
        $locationData = new LocationData(
            coordinate: $coordinate,
            countryCode: $countryCode,
            postalCode: $postalCode,
            locality: $locality,
            streetAddress: null,
            administrativeLevels: null,
            formattedAddress: null,
        );

        // Assert
        self::assertNull($locationData->getStreetAddress());
        self::assertNull($locationData->getAdministrativeLevels());
        self::assertNull($locationData->getFormattedAddress());
    }

    #[Test()]
    #[TestDox('Normalizes country code to uppercase')]
    #[Group('edge-case')]
    public function normalizes_country_code_to_uppercase(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 48.856_6,
                    'lng' => 2.352_2,
                ],
            ],
            'country_code' => 'fr', // lowercase
            'locality' => 'Paris',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertSame(CountryCode::FR, $locationData->getCountryCode());
    }

    #[Test()]
    #[DataProvider('provideExtracts_locality_using_correct_fallback_strategyCases')]
    #[TestDox('Extracts locality using correct fallback strategy: $expected')]
    #[Group('edge-case')]
    public function extracts_locality_using_correct_fallback_strategy(array $partialResult, string $expected): void
    {
        // Arrange
        $googleMapsResult = array_merge([
            'geometry' => [
                'location' => [
                    'lat' => 0.0,
                    'lng' => 0.0,
                ],
            ],
            'address_components' => [
                [
                    'types' => ['country'],
                    'short_name' => 'US',
                ],
            ],
        ], $partialResult);

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        self::assertSame($expected, $locationData->getLocality());
    }

    public static function provideExtracts_locality_using_correct_fallback_strategyCases(): iterable
    {
        yield 'direct locality field' => [
            ['locality' => 'Direct City'],
            'Direct City',
        ];

        yield 'locality in address components' => [
            ['address_components' => [['types' => ['locality'], 'long_name' => 'Component City']]],
            'Component City',
        ];

        yield 'postal town fallback' => [
            ['postal_town' => 'Postal Town'],
            'Postal Town',
        ];

        yield 'sublocality level 1 fallback' => [
            ['sublocality_level_1' => 'Sublocality'],
            'Sublocality',
        ];

        yield 'administrative area level 1 fallback' => [
            ['administrative_area_level_1' => 'Admin Area'],
            'Admin Area',
        ];

        yield 'postal code as last resort' => [
            ['postal_code' => '12345'],
            '12345',
        ];

        yield 'array locality uses first element' => [
            ['locality' => ['First', 'Second', 'Third']],
            'First',
        ];

        yield 'array postal town uses first element' => [
            ['postal_town' => ['Town1', 'Town2']],
            'Town1',
        ];
    }
}
