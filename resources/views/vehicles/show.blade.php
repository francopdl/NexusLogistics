@extends('layouts.app')

@section('title', $vehicle->license_plate . ' - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-car"></i> {{ $vehicle->license_plate }}</h1>
        <p class="text-muted">{{ $vehicle->manufacturer }} {{ $vehicle->model }} ({{ $vehicle->year }})</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información del Vehículo</h5>
            </div>
            <div class="card-body">
                <p><strong>Matrícula:</strong> {{ $vehicle->license_plate }}</p>
                <p><strong>Tipo:</strong> {{ $vehicle->vehicle_type }}</p>
                <p><strong>Marca:</strong> {{ $vehicle->manufacturer }}</p>
                <p><strong>Modelo:</strong> {{ $vehicle->model }}</p>
                <p><strong>Año:</strong> {{ $vehicle->year }}</p>
                <p><strong>Flota:</strong> <a href="{{ route('fleets.show', $vehicle->fleet) }}">{{ $vehicle->fleet->name }}</a></p>
                <p><strong>Creado:</strong> {{ $vehicle->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estado</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Estado Actual</h6>
                    <x-status-badge :status="$vehicle->status" type="vehicle" />
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Empresa</h6>
                    <p>
                        <a href="{{ route('companies.show', $vehicle->fleet->company) }}">
                            {{ $vehicle->fleet->company->name }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
