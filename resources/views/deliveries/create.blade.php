@extends('layouts.app')

@section('title', 'Nueva Entrega - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-plus-circle"></i> Nueva Entrega</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información de la Entrega</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('deliveries.store') }}" method="POST">
            @csrf

            <x-form-select 
                name="route_id" 
                label="Ruta" 
                required 
                :options="$routes->pluck('routeLabel', 'id')->toArray()"
            />

            <x-form-select 
                name="client_id" 
                label="Cliente" 
                required 
                :options="$clients->pluck('name', 'id')->toArray()"
            />

            <x-form-textarea 
                name="package_info" 
                label="Información del Paquete" 
                required 
                placeholder="Describe el contenido y características del paquete"
                :value="old('package_info')"
            />

            <x-form-select 
                name="status" 
                label="Estado" 
                required 
                :options="[
                    'pending' => 'Pendiente',
                    'in_transit' => 'En Tránsito',
                    'delivered' => 'Entregada',
                    'failed' => 'Fallida',
                    'cancelled' => 'Cancelada'
                ]"
            />

            <div class="row">
                <div class="col-md-6">
                    <x-form-input 
                        name="latitude" 
                        label="Latitud (Opcional)" 
                        type="number" 
                        step="0.0000001"
                        placeholder="-40.7128"
                    />
                </div>
                <div class="col-md-6">
                    <x-form-input 
                        name="longitude" 
                        label="Longitud (Opcional)" 
                        type="number" 
                        step="0.0000001"
                        placeholder="74.0060"
                    />
                </div>
            </div>

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Entrega
                </x-button>
                <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
