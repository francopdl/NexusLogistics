@extends('layouts.app')

@section('title', $user->name . ' - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-user"></i> {{ $user->name }}</h1>
        <p class="text-muted">{{ $user->email }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información del Usuario</h5>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Empresa:</strong> {{ $user->company?->name ?? 'Sin empresa' }}</p>
                <p><strong>Creado:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Última actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Roles y Permisos</h5>
            </div>
            <div class="card-body">
                @if($user->roles->count())
                    <div class="mb-3">
                        <h6 class="text-muted">Roles Asignados</h6>
                        @foreach($user->roles as $role)
                            <div class="mb-2">
                                <span class="badge bg-info" style="font-size: 12px;">{{ ucfirst($role->name) }}</span>
                                <p class="small text-muted mt-1">{{ $role->description }}</p>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <div>
                        <h6 class="text-muted">Permisos</h6>
                        @php
                            $permissions = $user->roles()
                                ->with('permissions')
                                ->get()
                                ->flatMap(fn($role) => $role->permissions)
                                ->unique('id');
                        @endphp
                        
                        @if($permissions->count())
                            <ul class="list-unstyled">
                                @foreach($permissions as $permission)
                                    <li>
                                        <i class="fas fa-check text-success"></i> 
                                        {{ $permission->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Sin permisos asignados</p>
                        @endif
                    </div>
                @else
                    <p class="text-muted">Sin roles asignados</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
