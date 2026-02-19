<?php

return [
    /**
     * Free Geolocation Services Configuration
     * 
     * Using completely free APIs:
     * - Nominatim (OpenStreetMap) for Geocoding & Reverse Geocoding
     * - OSRM (OpenStreetMap Routing) for Distance Calculation
     * - Leaflet.js for Map Display
     * 
     * No API keys required!
     */

    /**
     * Geocoding provider
     */
    'geocoding' => [
        'provider' => 'nominatim', // OpenStreetMap Nominatim
        'timeout' => 10,
        'base_url' => 'https://nominatim.openstreetmap.org',
    ],

    /**
     * Routing provider
     */
    'routing' => [
        'provider' => 'osrm', // Open Source Routing Machine
        'timeout' => 10,
        'base_url' => 'https://router.project-osrm.org',
    ],

    /**
     * Maps display settings (Leaflet + OpenStreetMap)
     */
    'maps' => [
        'provider' => 'leaflet',
        'tile_provider' => 'openstreetmap', // Free tile provider
        'default_center' => [
            'latitude' => 40.4168,    // Madrid, Spain
            'longitude' => -3.7038,
        ],
        'default_zoom' => 8,
        'map_height' => '500px',
    ],

    /**
     * Attribution notes
     */
    'attribution' => [
        'openstreetmap' => '© OpenStreetMap contributors',
        'leaflet' => 'Leaflet',
        'osrm' => 'Open Source Routing Machine',
    ],
];

