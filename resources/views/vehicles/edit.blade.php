@extends('layouts.app')

@section('title', 'Editar Vehículo - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-edit"></i> Editar Vehículo</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información del Vehículo</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
            @csrf
            @method('PUT')

            <x-form-select 
                name="fleet_id" 
                label="Flota" 
                required 
                :options="$fleets->pluck('name', 'id')->toArray()"
                :selected="$vehicle->fleet_id"
            />

            <x-form-input 
                name="license_plate" 
                label="Matrícula" 
                required 
                :value="$vehicle->license_plate"
                maxlength="15"
            />

            <x-form-input 
                name="vehicle_type" 
                label="Tipo de Vehículo" 
                required 
                :value="$vehicle->vehicle_type"
            />

            <x-form-input 
                name="manufacturer" 
                label="Marca" 
                required 
                :value="$vehicle->manufacturer"
            />

            <x-form-input 
                name="model" 
                label="Modelo" 
                required 
                :value="$vehicle->model"
            />

            <x-form-input 
                name="year" 
                label="Año" 
                type="number" 
                required 
                :value="$vehicle->year"
                min="2000"
                max="{{ date('Y') }}"
            />

            <x-form-select 
                name="status" 
                label="Estado" 
                required 
                :options="[
                    'available' => 'Disponible',
                    'in_use' => 'En Uso',
                    'maintenance' => 'En Mantenimiento',
                    'inactive' => 'Inactivo'
                ]"
                :selected="$vehicle->status"
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </x-button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
