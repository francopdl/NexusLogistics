@extends('layouts.app')

@section('title', 'Vehículos - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-car"></i> Gestión de Vehículos</h1>
    </div>
    <div class="col-md-4 text-end">
        <x-button type="primary" href="{{ route('vehicles.create') }}">
            <i class="fas fa-plus"></i> Nuevo Vehículo
        </x-button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Listado de Vehículos</h5>
    </div>
    <div class="card-body">
        @if($vehicles->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Tipo</th>
                            <th>Marca - Modelo</th>
                            <th>Año</th>
                            <th>Flota</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                            <tr>
                                <td><strong>{{ $vehicle->license_plate }}</strong></td>
                                <td>{{ $vehicle->vehicle_type }}</td>
                                <td>{{ $vehicle->manufacturer }} {{ $vehicle->model }}</td>
                                <td>{{ $vehicle->year }}</td>
                                <td>
                                    <a href="{{ route('fleets.show', $vehicle->fleet) }}">
                                        {{ $vehicle->fleet->name }}
                                    </a>
                                </td>
                                <td><x-status-badge :status="$vehicle->status" type="vehicle" /></td>
                                <td>
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:inline;">
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
            
            @if($vehicles instanceof \Illuminate\Pagination\Paginator)
                {{ $vehicles->links() }}
            @endif
        @else
            <p class="text-muted text-center mt-4">No hay vehículos registrados</p>
        @endif
    </div>
</div>
@endsection
