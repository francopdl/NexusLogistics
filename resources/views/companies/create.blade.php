@extends('layouts.app')

@section('title', 'Nueva Empresa - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-plus-circle"></i> Nueva Empresa</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información de la Empresa</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('companies.store') }}" method="POST">
            @csrf

            <x-form-input 
                name="name" 
                label="Nombre de la Empresa" 
                required 
                placeholder="Ingrese el nombre"
            />

            <x-form-input 
                name="email" 
                type="email"
                label="Email" 
                required 
                placeholder="empresa@example.com"
            />

            <x-form-input 
                name="phone" 
                label="Teléfono" 
                required 
                placeholder="+34 123 456 789"
            />

            <x-form-input 
                name="address" 
                label="Dirección" 
                required 
                placeholder="Calle y número"
            />

            <x-form-input 
                name="city" 
                label="Ciudad" 
                required 
                placeholder="Barcelona"
            />

            <x-form-input 
                name="country" 
                label="País" 
                required 
                placeholder="España"
            />

            <x-form-input 
                name="postal_code" 
                label="Código Postal" 
                required 
                placeholder="08001"
            />

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Empresa
                </x-button>
                <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
