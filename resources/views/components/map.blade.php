@props([
    'mapId' => 'map',
    'latitude' => 40.4168,
    'longitude' => -3.7038,
    'zoom' => 12,
    'markers' => [],
    'height' => '500px'
])

<div id="{{ $mapId }}" style="width: 100%; height: {{ $height }}; border-radius: 8px;"></div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Leaflet map
        const map = L.map('{{ $mapId }}').setView([{{ $latitude }}, {{ $longitude }}], {{ $zoom }});

        // Add OpenStreetMap tiles (free and open source)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Add markers
        @if(!empty($markers))
            const markers = {!! json_encode($markers) !!};
            markers.forEach(function(marker) {
                const lat = marker.lat || marker.latitude;
                const lng = marker.lng || marker.longitude;
                
                L.marker([lat, lng], {
                    title: marker.title || 'Marcador'
                })
                .bindPopup(marker.title || 'Ubicación')
                .addTo(map);
            });
        @endif

        // Add main marker
        L.marker([{{ $latitude }}, {{ $longitude }}], {
            title: 'Centro'
        })
        .bindPopup('Centro del mapa')
        .addTo(map);
    });
</script>
@endpush
