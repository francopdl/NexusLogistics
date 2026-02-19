@extends('layouts.app')

@section('title', 'Nueva Flota - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-plus-circle"></i> Nueva Flota</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información de la Flota</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('fleets.store') }}" method="POST">
            @csrf

            <x-form-select 
                name="company_id" 
                label="Empresa" 
                required 
                :options="$companies->pluck('name', 'id')->toArray()"
            />

            <x-form-input 
                name="name" 
                label="Nombre de la Flota" 
                required 
                placeholder="Flota Centro"
            />

            <x-form-textarea 
                name="description" 
                label="Descripción" 
                placeholder="Describe la flota y su propósito"
                :value="old('description')"
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Flota
                </x-button>
                <a href="{{ route('fleets.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
