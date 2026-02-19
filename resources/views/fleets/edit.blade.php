@extends('layouts.app')

@section('title', 'Editar Flota - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-edit"></i> Editar Flota</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información de la Flota</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('fleets.update', $fleet) }}" method="POST">
            @csrf
            @method('PUT')

            <x-form-select 
                name="company_id" 
                label="Empresa" 
                required 
                :options="$companies->pluck('name', 'id')->toArray()"
                :selected="$fleet->company_id"
            />

            <x-form-input 
                name="name" 
                label="Nombre de la Flota" 
                required 
                :value="$fleet->name"
            />

            <x-form-textarea 
                name="description" 
                label="Descripción" 
                :value="$fleet->description"
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </x-button>
                <a href="{{ route('fleets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
