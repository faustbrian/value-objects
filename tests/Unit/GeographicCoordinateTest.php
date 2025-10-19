<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cline\ValueObjects\GeographicCoordinate;
use Cline\ValueObjects\Latitude;
use Cline\ValueObjects\Longitude;

dataset('valid_coordinates', [
    'null island' => [0.0, 0.0],
    'london' => [51.5074, -0.1278],
    'paris' => [48.8566, 2.3522],
    'new york' => [40.7128, -74.0060],
    'tokyo' => [35.6762, 139.6503],
    'sydney' => [-33.8688, 151.2093],
    'north pole' => [90.0, 0.0],
    'south pole' => [-90.0, 0.0],
    'international date line east' => [0.0, 180.0],
    'international date line west' => [0.0, -180.0],
    'san francisco' => [37.7749, -122.4194],
]);

dataset('invalid_latitudes', [
    'too high' => [91.0, 0.0],
    'too low' => [-91.0, 0.0],
    'far too high' => [180.0, 0.0],
    'far too low' => [-180.0, 0.0],
]);

dataset('invalid_longitudes', [
    'too high' => [0.0, 181.0],
    'too low' => [0.0, -181.0],
    'far too high' => [0.0, 360.0],
    'far too low' => [0.0, -360.0],
]);

dataset('distance_pairs', [
    'london to paris' => [51.5074, -0.1278, 48.8566, 2.3522, 343570.0, 50000.0], // ~343km, tolerance 50km
    'new york to los angeles' => [40.7128, -74.0060, 34.0522, -118.2437, 3935746.0, 100000.0], // ~3936km, tolerance 100km
    'north pole to south pole' => [90.0, 0.0, -90.0, 0.0, 20003931.0, 100000.0], // ~20004km (half earth circumference)
    'same location' => [51.5074, -0.1278, 51.5074, -0.1278, 0.0, 0.1], // 0km
]);

dataset('google_maps_responses', [
    'with all fields' => [[
        'geometry' => [
            'location' => ['lat' => 51.5074, 'lng' => -0.1278],
            'location_type' => 'ROOFTOP',
        ],
        'place_id' => 'ChIJdd4hrwug2EcRmSrV3Vo6llI',
    ]],
    'without location type' => [[
        'geometry' => [
            'location' => ['lat' => 48.8566, 'lng' => 2.3522],
        ],
        'place_id' => 'ChIJD7fiBh9u5kcRYJSMaMOCCwQ',
    ]],
    'without place id' => [[
        'geometry' => [
            'location' => ['lat' => 40.7128, 'lng' => -74.0060],
            'location_type' => 'APPROXIMATE',
        ],
    ]],
    'minimal response' => [[
        'geometry' => [
            'location' => ['lat' => 35.6762, 'lng' => 139.6503],
        ],
    ]],
]);

describe('GeographicCoordinate', function (): void {
    describe('Happy Paths', function (): void {
        test('creates from valid coordinates', function (float $lat, float $lng): void {
            // Arrange
            $expectedLat = Latitude::createFromNumber($lat);
            $expectedLng = Longitude::createFromNumber($lng);

            // Act
            $coordinate = GeographicCoordinate::createFromCoordinates($lat, $lng);

            // Assert
            expect($coordinate->getLatitude()->toValue())->toBe($lat);
            expect($coordinate->getLongitude()->toValue())->toBe($lng);
            expect($coordinate->getPlaceId())->toBeNull();
            expect($coordinate->getLocationType())->toBeNull();
        })->with('valid_coordinates');

        test('creates from Google Maps response', function (array $response): void {
            // Arrange
            $lat = $response['geometry']['location']['lat'];
            $lng = $response['geometry']['location']['lng'];
            $locationType = $response['geometry']['location_type'] ?? null;
            $placeId = $response['place_id'] ?? null;

            // Act
            $coordinate = GeographicCoordinate::createFromGoogleMapsResponse($response);

            // Assert
            expect($coordinate->getLatitude()->toValue())->toBe($lat);
            expect($coordinate->getLongitude()->toValue())->toBe($lng);
            expect($coordinate->getLocationType())->toBe($locationType);
            expect($coordinate->getPlaceId())->toBe($placeId);
        })->with('google_maps_responses');

        test('calculates distance between coordinates', function (
            float $lat1,
            float $lng1,
            float $lat2,
            float $lng2,
            float $expectedDistance,
            float $tolerance
        ): void {
            // Arrange
            $origin = GeographicCoordinate::createFromCoordinates($lat1, $lng1);
            $destination = GeographicCoordinate::createFromCoordinates($lat2, $lng2);

            // Act
            $distance = $origin->distanceTo($destination);

            // Assert
            expect($distance)->toBeGreaterThanOrEqual($expectedDistance - $tolerance);
            expect($distance)->toBeLessThanOrEqual($expectedDistance + $tolerance);
        })->with('distance_pairs');

        test('calculates distance in kilometers', function (): void {
            // Arrange
            $london = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);
            $paris = GeographicCoordinate::createFromCoordinates(48.8566, 2.3522);

            // Act
            $distanceKm = $london->distanceToInKilometers($paris);

            // Assert
            expect($distanceKm)->toBeGreaterThan(340);
            expect($distanceKm)->toBeLessThan(350);
        });

        test('checks if coordinate is within radius', function (): void {
            // Arrange
            $center = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);
            $nearby = GeographicCoordinate::createFromCoordinates(51.5090, -0.1280);
            $faraway = GeographicCoordinate::createFromCoordinates(48.8566, 2.3522);

            // Act & Assert
            expect($nearby->isWithinRadius($center, 2000))->toBeTrue(); // Within 2km
            expect($faraway->isWithinRadius($center, 2000))->toBeFalse(); // Not within 2km
            expect($faraway->isWithinRadius($center, 400000))->toBeTrue(); // Within 400km
        });

        test('compares coordinates for equality', function (): void {
            // Arrange
            $coord1 = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);
            $coord2 = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);
            $coord3 = GeographicCoordinate::createFromCoordinates(48.8566, 2.3522);

            // Act & Assert
            expect($coord1->isEqualTo($coord2))->toBeTrue();
            expect($coord1->isEqualTo($coord3))->toBeFalse();
        });

        test('formats coordinate as string', function (): void {
            // Arrange
            $coordinate = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);

            // Act
            $result = $coordinate->toString();

            // Assert
            expect($result)->toBe('51.5074,-0.1278');
            expect($result)->toContain(',');
            expect($result)->not->toContain(' ');
        });

        test('creates with direct constructor', function (): void {
            // Arrange
            $latitude = Latitude::createFromNumber(45.0);
            $longitude = Longitude::createFromNumber(90.0);
            $locationType = 'ROOFTOP';
            $placeId = 'ChIJtest123';

            // Act
            $coordinate = new GeographicCoordinate(
                latitude: $latitude,
                longitude: $longitude,
                locationType: $locationType,
                placeId: $placeId
            );

            // Assert
            expect($coordinate->getLatitude())->toBe($latitude);
            expect($coordinate->getLongitude())->toBe($longitude);
            expect($coordinate->getLocationType())->toBe($locationType);
            expect($coordinate->getPlaceId())->toBe($placeId);
        });
    });

    describe('Sad Paths', function (): void {
        test('throws exception for invalid latitude', function (float $lat, float $lng): void {
            // Arrange & Act & Assert
            GeographicCoordinate::createFromCoordinates($lat, $lng);
        })->with('invalid_latitudes')->throws(OutOfRangeException::class);

        test('throws exception for invalid longitude', function (float $lat, float $lng): void {
            // Arrange & Act & Assert
            GeographicCoordinate::createFromCoordinates($lat, $lng);
        })->with('invalid_longitudes')->throws(OutOfRangeException::class);

        test('handles missing Google Maps response fields gracefully', function (): void {
            // Arrange
            $response = [
                'geometry' => [
                    'location' => ['lat' => 51.5074, 'lng' => -0.1278],
                ],
            ];

            // Act
            $coordinate = GeographicCoordinate::createFromGoogleMapsResponse($response);

            // Assert
            expect($coordinate->getLatitude()->toValue())->toBe(51.5074);
            expect($coordinate->getLongitude()->toValue())->toBe(-0.1278);
            expect($coordinate->getLocationType())->toBeNull();
            expect($coordinate->getPlaceId())->toBeNull();
        });
    });

    describe('Edge Cases', function (): void {
        test('handles null island coordinates', function (): void {
            // Arrange & Act
            $nullIsland = GeographicCoordinate::createFromCoordinates(0.0, 0.0);

            // Assert
            expect($nullIsland->getLatitude()->toValue())->toBe(0.0);
            expect($nullIsland->getLongitude()->toValue())->toBe(0.0);
            expect($nullIsland->toString())->toBe('0,0');
        });

        test('handles boundary coordinates', function (): void {
            // Arrange & Act
            $northPole = GeographicCoordinate::createFromCoordinates(90.0, 0.0);
            $southPole = GeographicCoordinate::createFromCoordinates(-90.0, 0.0);
            $dateLineEast = GeographicCoordinate::createFromCoordinates(0.0, 180.0);
            $dateLineWest = GeographicCoordinate::createFromCoordinates(0.0, -180.0);

            // Assert
            expect($northPole->getLatitude()->toValue())->toBe(90.0);
            expect($southPole->getLatitude()->toValue())->toBe(-90.0);
            expect($dateLineEast->getLongitude()->toValue())->toBe(180.0);
            expect($dateLineWest->getLongitude()->toValue())->toBe(-180.0);
        });

        test('distance to self is zero', function (): void {
            // Arrange
            $coordinate = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);

            // Act
            $distance = $coordinate->distanceTo($coordinate);

            // Assert
            expect($distance)->toBe(0.0);
        });

        test('handles antipodal points', function (): void {
            // Arrange
            $point1 = GeographicCoordinate::createFromCoordinates(0.0, 0.0);
            $point2 = GeographicCoordinate::createFromCoordinates(0.0, 180.0);

            // Act
            $distance = $point1->distanceTo($point2);
            $distanceKm = $point1->distanceToInKilometers($point2);

            // Assert
            expect($distance)->toBeGreaterThan(20000000); // > 20,000km in meters
            expect($distanceKm)->toBeGreaterThan(20000); // > 20,000km
        });

        test('preserves decimal precision', function (): void {
            // Arrange & Act
            $coordinate = GeographicCoordinate::createFromCoordinates(37.7749, -122.4194);

            // Assert
            expect($coordinate->getLatitude()->toValue())->toBe(37.7749);
            expect($coordinate->getLongitude()->toValue())->toBe(-122.4194);
            expect($coordinate->toString())->toBe('37.7749,-122.4194');
        });

        test('works without optional metadata', function (): void {
            // Arrange
            $latitude = Latitude::createFromNumber(45.0);
            $longitude = Longitude::createFromNumber(90.0);

            // Act
            $coordinate = new GeographicCoordinate($latitude, $longitude);

            // Assert
            expect($coordinate->getLocationType())->toBeNull();
            expect($coordinate->getPlaceId())->toBeNull();
        });

        test('equality ignores metadata', function (): void {
            // Arrange
            $coord1 = new GeographicCoordinate(
                Latitude::createFromNumber(51.5074),
                Longitude::createFromNumber(-0.1278),
                'ROOFTOP',
                'place123'
            );
            $coord2 = new GeographicCoordinate(
                Latitude::createFromNumber(51.5074),
                Longitude::createFromNumber(-0.1278),
                'APPROXIMATE',
                'place456'
            );
            $coord3 = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);

            // Act & Assert
            expect($coord1->isEqualTo($coord2))->toBeTrue();
            expect($coord1->isEqualTo($coord3))->toBeTrue();
            expect($coord2->isEqualTo($coord3))->toBeTrue();
        });

        test('point is within radius of itself', function (): void {
            // Arrange
            $coordinate = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);

            // Act & Assert
            expect($coordinate->isWithinRadius($coordinate, 0))->toBeTrue();
            expect($coordinate->isWithinRadius($coordinate, 1))->toBeTrue();
        });

        test('radius check at boundary', function (): void {
            // Arrange
            $center = GeographicCoordinate::createFromCoordinates(0.0, 0.0);
            $point = GeographicCoordinate::createFromCoordinates(0.01, 0.0);

            // Act
            $distance = $center->distanceTo($point);

            // Assert (approximately 1111 meters at equator for 0.01 degree)
            expect($point->isWithinRadius($center, $distance))->toBeTrue();
            expect($point->isWithinRadius($center, $distance - 0.1))->toBeFalse();
        });
    });

    describe('Regressions', function (): void {
        test('works despite createFromFloat bug in source', function (): void {
            // This test verifies the code works even though GeographicCoordinate
            // incorrectly calls Latitude::createFromFloat() instead of createFromNumber()
            // The test uses createFromCoordinates which has the same bug

            // Arrange & Act
            $coordinate = GeographicCoordinate::createFromCoordinates(51.5074, -0.1278);

            // Assert
            expect($coordinate)->toBeInstanceOf(GeographicCoordinate::class);
            expect($coordinate->getLatitude()->toValue())->toBe(51.5074);
            expect($coordinate->getLongitude()->toValue())->toBe(-0.1278);
        });

        test('Google Maps response parsing works despite createFromFloat bug', function (): void {
            // Arrange
            $response = [
                'geometry' => [
                    'location' => ['lat' => 51.5074, 'lng' => -0.1278],
                ],
            ];

            // Act
            $coordinate = GeographicCoordinate::createFromGoogleMapsResponse($response);

            // Assert
            expect($coordinate)->toBeInstanceOf(GeographicCoordinate::class);
            expect($coordinate->getLatitude()->toValue())->toBe(51.5074);
        });
    });
});