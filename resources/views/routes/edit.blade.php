@extends('layouts.app')

@section('title', 'Editar Ruta - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-edit"></i> Editar Ruta</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información de la Ruta</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('routes.update', $route) }}" method="POST">
            @csrf
            @method('PUT')

            <x-form-select 
                name="fleet_id" 
                label="Flota" 
                required 
                :options="$fleets->pluck('name', 'id')->toArray()"
                :selected="$route->fleet_id"
            />

            <x-form-input 
                name="origin" 
                label="Ciudad de Origen" 
                required 
                :value="$route->origin"
            />

            <x-form-input 
                name="destination" 
                label="Ciudad de Destino" 
                required 
                :value="$route->destination"
            />

            <div class="row">
                <div class="col-md-6">
                    <x-form-input 
                        name="estimated_departure" 
                        label="Salida Estimada" 
                        type="datetime-local" 
                        required
                        :value="$route->estimated_departure->format('Y-m-d\TH:i')"
                    />
                </div>
                <div class="col-md-6">
                    <x-form-input 
                        name="estimated_arrival" 
                        label="Llegada Estimada" 
                        type="datetime-local" 
                        required
                        :value="$route->estimated_arrival->format('Y-m-d\TH:i')"
                    />
                </div>
            </div>

            <x-form-select 
                name="status" 
                label="Estado" 
                required 
                :options="[
                    'pending' => 'Pendiente',
                    'in_progress' => 'En Progreso',
                    'completed' => 'Completada',
                    'cancelled' => 'Cancelada'
                ]"
                :selected="$route->status"
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </x-button>
                <a href="{{ route('routes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
