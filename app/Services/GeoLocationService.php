<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoLocationService
{
    // Obtener coordenadas desde dirección (Nominatim API)
    public function getCoordinatesFromAddress($address)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'NexusLogistics/1.0'
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ]);

            if ($response->successful() && $response->json()) {
                $location = $response->json('0');
                return [
                    'latitude' => (float)$location['lat'],
                    'longitude' => (float)$location['lon'],
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Nominatim geocoding error: ' . $e->getMessage());
        }

        return null;
    }

    // Obtener dirección desde coordenadas (Nominatim API)
    public function getAddressFromCoordinates($latitude, $longitude)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'NexusLogistics/1.0'
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                ]);

            if ($response->successful()) {
                return $response->json('address.city') ?? 
                       $response->json('address.town') ?? 
                       $response->json('address.village') ??
                       $response->json('display_name');
            }
        } catch (\Exception $e) {
            \Log::error('Nominatim reverse geocoding error: ' . $e->getMessage());
        }

        return null;
    }

    // Calcular distancia entre dos direcciones (OSRM API)
    public function calculateDistance($origin, $destination)
    {
        try {
            // Armar coordenadas
            $originCoords = $origin['latitude'] . ',' . $origin['longitude'];
            $destCoords = $destination['latitude'] . ',' . $destination['longitude'];

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'NexusLogistics/1.0'
                ])
                ->get('https://router.project-osrm.org/route/v1/driving/' . $originCoords . ';' . $destCoords, [
                    'overview' => 'false',
                    'steps' => 'false'
                ]);

            if ($response->successful()) {
                $route = $response->json('routes.0');
                if ($route) {
                    $distance = $route['distance'] ?? 0; // en metros
                    $duration = $route['duration'] ?? 0; // en segundos
                    
                    return [
                        'distance' => $distance,
                        'distance_km' => round($distance / 1000, 2),
                        'duration' => $this->formatDuration($duration),
                        'duration_seconds' => $duration,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('OSRM distance calculation error: ' . $e->getMessage());
        }

        return null;
    }

    // Formatear duración a texto legible
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . 's';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' min';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . 'h ' . $minutes . 'min';
        }
    }
}
