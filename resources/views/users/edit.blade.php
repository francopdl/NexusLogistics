@extends('layouts.app')

@section('title', 'Editar Usuario - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-edit"></i> Editar Usuario</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Información del Usuario</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <x-form-input 
                name="name" 
                label="Nombre Completo" 
                required 
                :value="$user->name"
            />

            <x-form-input 
                name="email" 
                label="Correo Electrónico" 
                type="email"
                required 
                :value="$user->email"
            />

            <x-form-input 
                name="password" 
                label="Contraseña (dejar en blanco para no cambiar)" 
                type="password"
                placeholder="Opcional"
            />

            <x-form-input 
                name="password_confirmation" 
                label="Confirmar Contraseña" 
                type="password"
            />

            <div class="form-group">
                <label class="form-label">Roles</label>
                <div class="border rounded p-3">
                    @php
                        $userRoleIds = $user->roles->pluck('id')->toArray();
                    @endphp
                    @foreach($roles as $role)
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                id="role_{{ $role->id }}"
                                name="roles[]"
                                value="{{ $role->id }}"
                                @if(in_array($role->id, $userRoleIds)) checked @endif
                                @error('roles') is-invalid @enderror
                            >
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ ucfirst($role->name) }}
                                <small class="text-muted d-block">{{ $role->description }}</small>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('roles')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <x-button type="primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </x-button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
