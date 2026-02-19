@extends('layouts.app')

@section('title', 'Nuevo Cliente - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-plus-circle"></i> Nuevo Cliente</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información del Cliente</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf

            <x-form-select 
                name="company_id" 
                label="Empresa" 
                required 
                :options="$companies->pluck('name', 'id')->toArray()"
            />

            <x-form-input 
                name="name" 
                label="Nombre del Cliente" 
                required 
                placeholder="Ingrese el nombre"
            />

            <x-form-input 
                name="email" 
                type="email"
                label="Email" 
                required 
                placeholder="cliente@example.com"
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

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Cliente
                </x-button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
