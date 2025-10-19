<?php declare(strict_types=1);

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

#[CoversClass(LocationData::class)]
#[Small]
final class LocationDataTest extends TestCase
{
    #[Test]
    #[TestDox('Creates location data from complete Google Maps result')]
    #[Group('happy-path')]
    public function createsLocationDataFromCompleteGoogleMapsResult(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 59.3293,
                    'lng' => 18.0686,
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
        $this->assertInstanceOf(LocationData::class, $locationData);
        $this->assertEquals(59.3293, $locationData->getCoordinate()->getLatitude()->toValue());
        $this->assertEquals(18.0686, $locationData->getCoordinate()->getLongitude()->toValue());
        $this->assertEquals('ROOFTOP', $locationData->getCoordinate()->getLocationType());
        $this->assertEquals('ChIJywtkGTF2X0YRZnedZ9MnDag', $locationData->getCoordinate()->getPlaceId());
        $this->assertEquals(CountryCode::SE, $locationData->getCountryCode());
        $this->assertEquals('111 22', $locationData->getPostalCode()->toString());
        $this->assertEquals('Stockholm', $locationData->getLocality());
        $this->assertEquals('Drottninggatan 1', $locationData->getStreetAddress());
        $this->assertEquals('Drottninggatan 1, 111 22 Stockholm, Sweden', $locationData->getFormattedAddress());
        $this->assertCount(2, $locationData->getAdministrativeLevels());
    }

    #[Test]
    #[TestDox('Creates location data from minimal Google Maps result')]
    #[Group('happy-path')]
    public function createsLocationDataFromMinimalGoogleMapsResult(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 40.7128,
                    'lng' => -74.0060,
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
        $this->assertInstanceOf(LocationData::class, $locationData);
        $this->assertEquals(40.7128, $locationData->getCoordinate()->getLatitude()->toValue());
        $this->assertEquals(-74.0060, $locationData->getCoordinate()->getLongitude()->toValue());
        $this->assertNull($locationData->getCoordinate()->getLocationType());
        $this->assertNull($locationData->getCoordinate()->getPlaceId());
        $this->assertEquals(CountryCode::US, $locationData->getCountryCode());
        $this->assertEquals('', $locationData->getPostalCode()->toString());
        $this->assertEquals('New York', $locationData->getLocality());
        $this->assertNull($locationData->getStreetAddress());
        $this->assertNull($locationData->getFormattedAddress());
        $this->assertEmpty($locationData->getAdministrativeLevels());
    }

    #[Test]
    #[TestDox('Creates location data through direct constructor with all fields')]
    #[Group('happy-path')]
    public function createsLocationDataThroughDirectConstructorWithAllFields(): void
    {
        // Arrange
        $coordinate = new GeographicCoordinate(
            latitude: Latitude::createFromNumber(51.5074),
            longitude: Longitude::createFromNumber(-0.1278),
            locationType: 'ROOFTOP',
            placeId: 'ChIJdd4hrwug2EcRmSrV3Vo6llI'
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
            formattedAddress: $formattedAddress
        );

        // Assert
        $this->assertInstanceOf(LocationData::class, $locationData);
        $this->assertSame($coordinate, $locationData->getCoordinate());
        $this->assertSame($countryCode, $locationData->getCountryCode());
        $this->assertSame($postalCode, $locationData->getPostalCode());
        $this->assertEquals($locality, $locationData->getLocality());
        $this->assertEquals($streetAddress, $locationData->getStreetAddress());
        $this->assertEquals($administrativeLevels, $locationData->getAdministrativeLevels());
        $this->assertEquals($formattedAddress, $locationData->getFormattedAddress());
    }

    #[Test]
    #[TestDox('Creates location data through direct constructor with minimal fields')]
    #[Group('happy-path')]
    public function createsLocationDataThroughDirectConstructorWithMinimalFields(): void
    {
        // Arrange
        $coordinate = new GeographicCoordinate(
            latitude: Latitude::createFromNumber(48.8566),
            longitude: Longitude::createFromNumber(2.3522)
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
            formattedAddress: null
        );

        // Assert
        $this->assertInstanceOf(LocationData::class, $locationData);
        $this->assertSame($coordinate, $locationData->getCoordinate());
        $this->assertSame($countryCode, $locationData->getCountryCode());
        $this->assertSame($postalCode, $locationData->getPostalCode());
        $this->assertEquals($locality, $locationData->getLocality());
        $this->assertNull($locationData->getStreetAddress());
        $this->assertNull($locationData->getAdministrativeLevels());
        $this->assertNull($locationData->getFormattedAddress());
    }

    #[Test]
    #[TestDox('Throws exception when country code is missing from Google Maps result')]
    #[Group('sad-path')]
    public function throwsExceptionWhenCountryCodeIsMissingFromGoogleMapsResult(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 40.7128,
                    'lng' => -74.0060,
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

    #[Test]
    #[TestDox('Throws exception when locality cannot be determined from any fallback')]
    #[Group('sad-path')]
    public function throwsExceptionWhenLocalityCannotBeDeterminedFromAnyFallback(): void
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

    #[Test]
    #[TestDox('Handles empty postal code for locations without postal codes')]
    #[Group('edge-case')]
    public function handlesEmptyPostalCodeForLocationsWithoutPostalCodes(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => -1.2921,
                    'lng' => 36.8219,
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
        $this->assertEquals('', $locationData->getPostalCode()->toString());
        $this->assertEquals(CountryCode::KE, $locationData->getPostalCode()->getCountryCode());
    }

    #[Test]
    #[TestDox('Uses postal town as locality fallback when locality is missing')]
    #[Group('edge-case')]
    public function usesPostalTownAsLocalityFallbackWhenLocalityIsMissing(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 51.5074,
                    'lng' => -0.1278,
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
        $this->assertEquals('London', $locationData->getLocality());
    }

    #[Test]
    #[TestDox('Uses sublocality level 1 as locality fallback')]
    #[Group('edge-case')]
    public function usesSublocalityLevel1AsLocalityFallback(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 35.6762,
                    'lng' => 139.6503,
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
        $this->assertEquals('Shibuya', $locationData->getLocality());
    }

    #[Test]
    #[TestDox('Uses administrative area level 1 as locality fallback')]
    #[Group('edge-case')]
    public function usesAdministrativeAreaLevel1AsLocalityFallback(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 37.7749,
                    'lng' => -122.4194,
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
        $this->assertEquals('California', $locationData->getLocality());
    }

    #[Test]
    #[TestDox('Uses postal code as last resort locality fallback')]
    #[Group('edge-case')]
    public function usesPostalCodeAsLastResortLocalityFallback(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 40.7128,
                    'lng' => -74.0060,
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
        $this->assertEquals('10001', $locationData->getLocality());
    }

    #[Test]
    #[TestDox('Handles locality as array by using first element')]
    #[Group('edge-case')]
    public function handlesLocalityAsArrayByUsingFirstElement(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 52.5200,
                    'lng' => 13.4050,
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
        $this->assertEquals('Berlin', $locationData->getLocality());
    }

    #[Test]
    #[TestDox('Extracts multiple administrative levels from Google Maps result')]
    #[Group('edge-case')]
    public function extractsMultipleAdministrativeLevelsFromGoogleMapsResult(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 34.0522,
                    'lng' => -118.2437,
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
        $this->assertCount(5, $administrativeLevels);
        $this->assertEquals('California', $administrativeLevels['administrative_area_level_1']);
        $this->assertEquals('Los Angeles County', $administrativeLevels['administrative_area_level_2']);
        $this->assertEquals('Los Angeles', $administrativeLevels['administrative_area_level_3']);
        $this->assertEquals('District 4', $administrativeLevels['administrative_area_level_4']);
        $this->assertEquals('Subdistrict 5', $administrativeLevels['administrative_area_level_5']);
    }

    #[Test]
    #[TestDox('Handles null optional fields gracefully')]
    #[Group('edge-case')]
    public function handlesNullOptionalFieldsGracefully(): void
    {
        // Arrange
        $coordinate = new GeographicCoordinate(
            latitude: Latitude::createFromNumber(0.0),
            longitude: Longitude::createFromNumber(0.0)
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
            formattedAddress: null
        );

        // Assert
        $this->assertNull($locationData->getStreetAddress());
        $this->assertNull($locationData->getAdministrativeLevels());
        $this->assertNull($locationData->getFormattedAddress());
    }

    #[Test]
    #[TestDox('Normalizes country code to uppercase')]
    #[Group('edge-case')]
    public function normalizesCountryCodeToUppercase(): void
    {
        // Arrange
        $googleMapsResult = [
            'geometry' => [
                'location' => [
                    'lat' => 48.8566,
                    'lng' => 2.3522,
                ],
            ],
            'country_code' => 'fr', // lowercase
            'locality' => 'Paris',
        ];

        // Act
        $locationData = LocationData::createFromGoogleMapsResult($googleMapsResult);

        // Assert
        $this->assertEquals(CountryCode::FR, $locationData->getCountryCode());
    }

    public static function localityFallbackProvider(): array
    {
        return [
            'direct locality field' => [
                ['locality' => 'Direct City'],
                'Direct City',
            ],
            'locality in address components' => [
                ['address_components' => [['types' => ['locality'], 'long_name' => 'Component City']]],
                'Component City',
            ],
            'postal town fallback' => [
                ['postal_town' => 'Postal Town'],
                'Postal Town',
            ],
            'sublocality level 1 fallback' => [
                ['sublocality_level_1' => 'Sublocality'],
                'Sublocality',
            ],
            'administrative area level 1 fallback' => [
                ['administrative_area_level_1' => 'Admin Area'],
                'Admin Area',
            ],
            'postal code as last resort' => [
                ['postal_code' => '12345'],
                '12345',
            ],
            'array locality uses first element' => [
                ['locality' => ['First', 'Second', 'Third']],
                'First',
            ],
            'array postal town uses first element' => [
                ['postal_town' => ['Town1', 'Town2']],
                'Town1',
            ],
        ];
    }

    #[Test]
    #[DataProvider('localityFallbackProvider')]
    #[TestDox('Extracts locality using correct fallback strategy: $expected')]
    #[Group('edge-case')]
    public function extractsLocalityUsingCorrectFallbackStrategy(array $partialResult, string $expected): void
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
        $this->assertEquals($expected, $locationData->getLocality());
    }
}