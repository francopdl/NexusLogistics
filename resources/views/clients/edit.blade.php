@extends('layouts.app')

@section('title', 'Editar Cliente - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-edit"></i> Editar Cliente</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información del Cliente</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <x-form-select 
                name="company_id" 
                label="Empresa" 
                required 
                :options="$companies->pluck('name', 'id')->toArray()"
                :selected="$client->company_id"
            />

            <x-form-input 
                name="name" 
                label="Nombre del Cliente" 
                required 
                :value="$client->name"
            />

            <x-form-input 
                name="email" 
                type="email"
                label="Email" 
                required 
                :value="$client->email"
            />

            <x-form-input 
                name="phone" 
                label="Teléfono" 
                required 
                :value="$client->phone"
            />

            <x-form-input 
                name="address" 
                label="Dirección" 
                required 
                :value="$client->address"
            />

            <x-form-input 
                name="city" 
                label="Ciudad" 
                required 
                :value="$client->city"
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </x-button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
