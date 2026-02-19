@extends('layouts.app')

@section('title', $fleet->name . ' - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-boxes"></i> {{ $fleet->name }}</h1>
        <p class="text-muted">{{ $fleet->company->name }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('fleets.edit', $fleet) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('fleets.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información General</h5>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $fleet->name }}</p>
                <p><strong>Empresa:</strong> <a href="{{ route('companies.show', $fleet->company) }}">{{ $fleet->company->name }}</a></p>
                <p><strong>Descripción:</strong></p>
                <p>{{ $fleet->description ?? 'Sin descripción' }}</p>
                <p><strong>Creada:</strong> {{ $fleet->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Vehículos</h6>
                        <h2 class="text-primary">{{ $fleet->vehicles()->count() }}</h2>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Rutas</h6>
                        <h2 class="text-info">{{ $fleet->routes()->count() }}</h2>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Disponibles</h6>
                        <h2 class="text-success">{{ $fleet->vehicles()->where('status', 'available')->count() }}</h2>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">En Uso</h6>
                        <h2 class="text-warning">{{ $fleet->vehicles()->where('status', 'in_use')->count() }}</h2>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-car"></i> Vehículos de la Flota</h5>
                    <a href="{{ route('vehicles.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Agregar Vehículo
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($fleet->vehicles()->count())
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Tipo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Año</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fleet->vehicles as $vehicle)
                                <tr>
                                    <td><strong>{{ $vehicle->license_plate }}</strong></td>
                                    <td>{{ $vehicle->vehicle_type }}</td>
                                    <td>{{ $vehicle->manufacturer }}</td>
                                    <td>{{ $vehicle->model }}</td>
                                    <td>{{ $vehicle->year }}</td>
                                    <td><x-status-badge :status="$vehicle->status" type="vehicle" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No hay vehículos en esta flota</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
