@extends('layouts.app')

@section('title', 'Editar Empresa - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-edit"></i> Editar Empresa</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información de la Empresa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('companies.update', $company) }}" method="POST">
            @csrf
            @method('PUT')

            <x-form-input 
                name="name" 
                label="Nombre de la Empresa" 
                required 
                :value="$company->name"
            />

            <x-form-input 
                name="email" 
                type="email"
                label="Email" 
                required 
                :value="$company->email"
            />

            <x-form-input 
                name="phone" 
                label="Teléfono" 
                required 
                :value="$company->phone"
            />

            <x-form-input 
                name="address" 
                label="Dirección" 
                required 
                :value="$company->address"
            />

            <x-form-input 
                name="city" 
                label="Ciudad" 
                required 
                :value="$company->city"
            />

            <x-form-input 
                name="country" 
                label="País" 
                required 
                :value="$company->country"
            />

            <x-form-input 
                name="postal_code" 
                label="Código Postal" 
                required 
                :value="$company->postal_code"
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </x-button>
                <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
