@extends('layouts.app')

@section('title', 'Rutas - Nexus Logistics')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<style>
    .map-container {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: block;
        position: relative;
        width: 100%;
        min-height: 500px;
    }
    
    .map-small {
        height: 300px;
        width: 100%;
    }
    
    .map-large {
        height: 500px !important;
        width: 100% !important;
        display: block !important;
        position: relative !important;
        background-color: #e5e3df;
    }
    
    #mapIndex {
        height: 500px !important;
        width: 100% !important;
        display: block !important;
        position: relative !important;
    }
    
    .leaflet-control-attribution {
        background: rgba(255,255,255,0.8) !important;
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-map"></i> Gestión de Rutas</h1>
    </div>
    <div class="col-md-4 text-end">
        <x-button type="primary" href="{{ route('routes.create') }}">
            <i class="fas fa-plus"></i> Nueva Ruta
        </x-button>
    </div>
</div>

<!-- Mapa General de Rutas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map-location-dot"></i> Vista General de Rutas</h5>
            </div>
            <div class="card-body p-0">
                <div class="map-container">
                    <div id="mapIndex" class="map-large"></div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="fas fa-circle" style="color: #27ae60;"></i> <strong>Verde:</strong> Origen de ruta | 
                    <i class="fas fa-circle" style="color: #e74c3c;"></i> <strong>Rojo:</strong> Destino | 
                    <i class="fas fa-circle" style="color: #3498db;"></i> <strong>Azul:</strong> En progreso | 
                    <i class="fas fa-circle" style="color: #f39c12;"></i> <strong>Naranja:</strong> Pendiente
                </small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Listado de Rutas</h5>
    </div>
    <div class="card-body">
        @if($routes->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Flota</th>
                            <th>Distancia</th>
                            <th>Salida</th>
                            <th>Llegada</th>
                            <th>Estado</th>
                            <th>Entregas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($routes as $route)
                            <tr>
                                <td>{{ strlen($route->origin) > 20 ? substr($route->origin, 0, 20) . '...' : $route->origin }}</td>
                                <td>{{ strlen($route->destination) > 20 ? substr($route->destination, 0, 20) . '...' : $route->destination }}</td>
                                <td>
                                    <a href="{{ route('fleets.show', $route->fleet) }}">
                                        {{ $route->fleet->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($route->distance_km)
                                        <span class="badge bg-info">{{ $route->distance_km }} km</span>
                                    @else
                                        <span class="badge bg-secondary">Sin calcular</span>
                                    @endif
                                </td>
                                <td>{{ $route->estimated_departure->format('d/m H:i') }}</td>
                                <td>{{ $route->estimated_arrival->format('d/m H:i') }}</td>
                                <td><x-status-badge :status="$route->status" type="route" /></td>
                                <td><span class="badge bg-info">{{ $route->deliveries()->count() }}</span></td>
                                <td>
                                    <a href="{{ route('routes.show', $route) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('routes.edit', $route) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('routes.destroy', $route) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($routes instanceof \Illuminate\Pagination\Paginator)
                {{ $routes->links() }}
            @endif
        @else
            <p class="text-muted text-center mt-4">No hay rutas registradas</p>
        @endif
    </div>
</div>

@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    console.log('PRUEBA 1: Script de rutas ejecutándose');
    console.log('PRUEBA 2: ¿Leaflet disponible?', window.L);
    
    function initializeMap() {
        console.log('=== Iniciando mapa ===');
        
        // Obtener el contenedor
        const mapContainer = document.getElementById('mapIndex');
        if (!mapContainer) {
            console.error('❌ No se encontró mapIndex');
            return;
        }
        
        // FORZAR ALTURA
        console.log('Altura actual:', mapContainer.offsetHeight);
        mapContainer.style.height = '500px';
        mapContainer.style.width = '100%';
        mapContainer.style.display = 'block';
        console.log('Altura después de forzar:', mapContainer.offsetHeight);
        
        if (typeof L === 'undefined') {
            console.error('❌ Leaflet no está disponible');
            return;
        }
        
        console.log('✓ Leaflet cargado correctamente');
        
        const mapIndex = L.map('mapIndex', {
            preferCanvas: true
        }).setView([40.4168, -3.7038], 5);
        
        console.log('✓ Mapa creado');
        
        // Forzar redibujado
        setTimeout(() => {
            mapIndex.invalidateSize();
            console.log('✓ Redibujado forzado');
        }, 100);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(mapIndex);
        
        console.log('✓ Mapa base cargado');
        
        const routesData = @json($routes instanceof \Illuminate\Pagination\AbstractPaginator ? $routes->items() : $routes->toArray());
        console.log('Total rutas:', routesData ? routesData.length : 0);
        
        if (!routesData || routesData.length === 0) {
            console.warn('⚠️ No hay rutas con coordenadas');
            return;
        }
        
        const bounds = L.latLngBounds();
        let count = 0;
        
        routesData.forEach(route => {
            if (route.origin_latitude && route.destination_latitude) {
                count++;
                const oLat = parseFloat(route.origin_latitude);
                const oLng = parseFloat(route.origin_longitude);
                const dLat = parseFloat(route.destination_latitude);
                const dLng = parseFloat(route.destination_longitude);
                
                console.log(`Ruta ${count}: (${oLat},${oLng}) → (${dLat},${dLng})`);
                
                // Origen
                L.circleMarker([oLat, oLng], {
                    radius: 10,
                    fillColor: '#27ae60',
                    color: '#fff',
                    weight: 3,
                    fillOpacity: 0.85
                }).addTo(mapIndex).bindPopup(`<strong>📍 ${route.origin}</strong>`);
                
                // Destino
                L.circleMarker([dLat, dLng], {
                    radius: 10,
                    fillColor: '#e74c3c',
                    color: '#fff',
                    weight: 3,
                    fillOpacity: 0.85
                }).addTo(mapIndex).bindPopup(`<strong>🚩 ${route.destination}</strong>`);
                
                // Línea
                let color = '#f39c12';
                if (route.status === 'in_progress') color = '#3498db';
                else if (route.status === 'completed') color = '#27ae60';
                
                L.polyline([[oLat, oLng], [dLat, dLng]], {
                    color: color,
                    weight: 4,
                    opacity: 0.8
                }).addTo(mapIndex);
                
                bounds.extend([oLat, oLng]);
                bounds.extend([dLat, dLng]);
            }
        });
        
        console.log(`✓ ${count} ruta(s) dibujada(s)`);
        
        if (count > 0 && bounds.isValid()) {
            mapIndex.fitBounds(bounds, { padding: [80, 80] });
            console.log('✓ Zoom ajustado');
        }
        
        console.log('=== MAPA LISTO ===');
    }
    
    // Ejecutar cuando esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeMap);
    } else {
        setTimeout(initializeMap, 100);
    }
</script>
