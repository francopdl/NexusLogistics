@extends('layouts.app')

@section('title', 'Nuevo Vehículo - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-plus-circle"></i> Nuevo Vehículo</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información del Vehículo</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('vehicles.store') }}" method="POST">
            @csrf

            <x-form-select 
                name="fleet_id" 
                label="Flota" 
                required 
                :options="$fleets->pluck('name', 'id')->toArray()"
            />

            <x-form-input 
                name="license_plate" 
                label="Matrícula" 
                required 
                placeholder="XXX-1234"
                maxlength="15"
            />

            <x-form-input 
                name="vehicle_type" 
                label="Tipo de Vehículo" 
                required 
                placeholder="Camión, Van, etc."
            />

            <x-form-input 
                name="manufacturer" 
                label="Marca" 
                required 
                placeholder="Toyota, Volvo, etc."
            />

            <x-form-input 
                name="model" 
                label="Modelo" 
                required 
                placeholder="Hiace, FH16, etc."
            />

            <x-form-input 
                name="year" 
                label="Año" 
                type="number" 
                required 
                placeholder="2024"
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
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Vehículo
                </x-button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
