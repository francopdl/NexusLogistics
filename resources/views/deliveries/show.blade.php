@extends('layouts.app')

@section('title', 'Entrega - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-box"></i> Entrega #{{ $delivery->id }}</h1>
        <p class="text-muted">Ruta: {{ $delivery->route->origin }} → {{ $delivery->route->destination }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información del Paquete</h5>
            </div>
            <div class="card-body">
                <p><strong>Información:</strong></p>
                <p>{{ $delivery->package_info }}</p>
                <hr>
                <p><strong>Cliente:</strong> <a href="{{ route('clients.show', $delivery->client) }}">{{ $delivery->client->name }}</a></p>
                <p><strong>Ruta:</strong> <a href="{{ route('routes.show', $delivery->route) }}">{{ $delivery->route->origin }} → {{ $delivery->route->destination }}</a></p>
                <p><strong>Creada:</strong> {{ $delivery->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estado y Ubicación</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Estado Actual</h6>
                    <x-status-badge :status="$delivery->status" type="delivery" />
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Ubicación Geográfica</h6>
                    @if($delivery->latitude && $delivery->longitude)
                        <p>
                            <strong>Latitud:</strong> {{ $delivery->latitude }}<br>
                            <strong>Longitud:</strong> {{ $delivery->longitude }}
                        </p>
                        <div class="mt-2" id="delivery-map" style="height: 300px; border-radius: .25rem; overflow: hidden;">
                            @php
                                $marker = (object)[
                                    'lat' => $delivery->latitude,
                                    'lng' => $delivery->longitude,
                                    'title' => 'Entrega #' . $delivery->id
                                ];
                            @endphp
                            <x-map 
                                map-id="delivery-map" 
                                :latitude="$delivery->latitude" 
                                :longitude="$delivery->longitude"
                                :markers="[$marker]"
                            />
                        </div>
                    @else
                        <p class="text-muted">Sin ubicación registrada</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detalles de la Ruta</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="text-muted">Origen</h6>
                        <p>{{ $delivery->route->origin }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Destino</h6>
                        <p>{{ $delivery->route->destination }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Salida</h6>
                        <p>{{ $delivery->route->estimated_departure->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted">Llegada</h6>
                        <p>{{ $delivery->route->estimated_arrival->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
