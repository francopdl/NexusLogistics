@extends('layouts.app')

@section('title', 'Ruta - Nexus Logistics')

@section('styles')
<style>
    .map-container {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .map-large {
        height: 500px;
        width: 100%;
    }
    
    .route-info-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .info-item {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
        border-left: 4px solid #3498db;
    }
    
    .info-item strong {
        color: #2c3e50;
        display: block;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
    .info-item span {
        color: #555;
        font-size: 1.1rem;
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-map"></i> Ruta: {{ $route->origin }} → {{ $route->destination }}</h1>
        <p class="text-muted">{{ $route->fleet->name }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('routes.edit', $route) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('routes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<!-- Mapa Interactivo de la Ruta -->
@if($route->origin_latitude && $route->destination_latitude)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map-location-dot"></i> Visualización del Trayecto</h5>
            </div>
            <div class="card-body p-0">
                <div class="map-container">
                    <div id="mapRoute" class="map-large"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información de la Ruta</h5>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <strong><i class="fas fa-location-dot"></i> Origen</strong>
                        <span>{{ $route->origin }}</span>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-flag-checkered"></i> Destino</strong>
                        <span>{{ $route->destination }}</span>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-warehouse"></i> Flota</strong>
                        <span><a href="{{ route('fleets.show', $route->fleet) }}">{{ $route->fleet->name }}</a></span>
                    </div>
                    @if($route->distance_km)
                    <div class="info-item">
                        <strong><i class="fas fa-road"></i> Distancia</strong>
                        <span>{{ $route->distance_km }} km</span>
                    </div>
                    @endif
                </div>

                <hr>

                <p><strong>Salida estimada:</strong> {{ $route->estimated_departure->format('d/m/Y H:i') }}</p>
                <p><strong>Llegada estimada:</strong> {{ $route->estimated_arrival->format('d/m/Y H:i') }}</p>
                
                @if($route->duration_seconds)
                <p><strong>Duración estimada:</strong> 
                    @php
                        $hours = floor($route->duration_seconds / 3600);
                        $minutes = floor(($route->duration_seconds % 3600) / 60);
                    @endphp
                    {{ $hours }}h {{ $minutes }}m
                </p>
                @endif

                <p><strong>Creada:</strong> {{ $route->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estado y Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Estado Actual</h6>
                    <x-status-badge :status="$route->status" type="route" />
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Entregas Asociadas</h6>
                    <h2 class="text-primary">{{ $route->deliveries()->count() }}</h2>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Entregas Completadas</h6>
                    <h2 class="text-success">{{ $route->deliveries()->where('status', 'delivered')->count() }} / {{ $route->deliveries()->count() }}</h2>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Progreso</h6>
                    @php
                        $total = $route->deliveries()->count() ?: 1;
                        $completed = $route->deliveries()->where('status', 'delivered')->count();
                        $percentage = ($completed / $total) * 100;
                    @endphp
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($percentage, 0) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> Entregas en esta Ruta</h5>
            </div>
            <div class="card-body">
                @if($route->deliveries()->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Información del Paquete</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($route->deliveries as $delivery)
                                    <tr>
                                        <td>{{ $delivery->package_info }}</td>
                                        <td><a href="{{ route('clients.show', $delivery->client) }}">{{ $delivery->client->name }}</a></td>
                                        <td><x-status-badge :status="$delivery->status" type="delivery" /></td>
                                        <td>
                                            <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No hay entregas en esta ruta</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @if($route->origin_latitude && $route->destination_latitude)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                console.log('Inicializando mapa de ruta...');
                
                // Coordenadas
                const origin = [{{ $route->origin_latitude }}, {{ $route->origin_longitude }}];
                const destination = [{{ $route->destination_latitude }}, {{ $route->destination_longitude }}];
                
                // Centro del mapa
                const centerLat = (origin[0] + destination[0]) / 2;
                const centerLng = (origin[1] + destination[1]) / 2;
                
                // Inicializar mapa
                const mapRoute = L.map('mapRoute').setView([centerLat, centerLng], 10);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(mapRoute);
                
                // Marcador de origen (verde)
                L.circleMarker(origin, {
                    radius: 12,
                    fillColor: '#27ae60',
                    color: '#fff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(mapRoute)
                  .bindPopup('<div class="text-center"><strong>📍 Origen</strong><br/>{{ $route->origin }}</div>')
                  .openPopup();
                
                // Marcador de destino (rojo)
                L.circleMarker(destination, {
                    radius: 12,
                    fillColor: '#e74c3c',
                    color: '#fff',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(mapRoute)
                  .bindPopup('<div class="text-center"><strong>🚩 Destino</strong><br/>{{ $route->destination }}</div>');
                
                // Línea de la ruta (azul)
                L.polyline([origin, destination], {
                    color: '#3498db',
                    weight: 4,
                    opacity: 0.8,
                    dashArray: '5, 5'
                }).addTo(mapRoute)
                  .bindPopup('<strong>Trayecto:</strong> {{ $route->origin }} → {{ $route->destination }}');
                
                // Radio de 5km alrededor de origen y destino
                L.circle(origin, {
                    color: '#27ae60',
                    fillColor: '#27ae60',
                    fillOpacity: 0.1,
                    radius: 5000
                }).addTo(mapRoute);
                
                L.circle(destination, {
                    color: '#e74c3c',
                    fillColor: '#e74c3c',
                    fillOpacity: 0.1,
                    radius: 5000
                }).addTo(mapRoute);
                
                // Ajustar zoom a los marcadores
                const bounds = L.latLngBounds([origin, destination]);
                mapRoute.fitBounds(bounds, { padding: [100, 100] });
                
                console.log('Mapa de ruta inicializado correctamente');
            } catch (error) {
                console.error('Error al inicializar mapa:', error);
            }
        });
    </script>
    @else
    <script>
        // Si no hay coordenadas, mostrar mensaje
        document.addEventListener('DOMContentLoaded', function() {
            const mapContainer = document.getElementById('mapRoute');
            if (mapContainer) {
                mapContainer.innerHTML = '<div class="alert alert-warning m-3">Las coordenadas de esta ruta aún no han sido calculadas. Por favor, edite la ruta para actualizar las coordenadas.</div>';
            }
        });
    </script>
    @endif
@endsection
