@extends('layouts.app')

@section('title', $company->name . ' - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-building"></i> {{ $company->name }}</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('companies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información General</h5>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $company->name }}</p>
                <p><strong>Email:</strong> <a href="mailto:{{ $company->email }}">{{ $company->email }}</a></p>
                <p><strong>Teléfono:</strong> {{ $company->phone }}</p>
                <p><strong>Dirección:</strong> {{ $company->address }}</p>
                <p><strong>Ciudad:</strong> {{ $company->city }}</p>
                <p><strong>País:</strong> {{ $company->country }}</p>
                <p><strong>Código Postal:</strong> {{ $company->postal_code }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estadísticas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Usuarios</h6>
                        <h2 class="text-primary">{{ $company->users()->count() }}</h2>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Clientes</h6>
                        <h2 class="text-success">{{ $company->clients()->count() }}</h2>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Flotas</h6>
                        <h2 class="text-info">{{ $company->fleets()->count() }}</h2>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Vehículos</h6>
                        <h2 class="text-warning">{{ $company->fleets()->with('vehicles')->get()->sum(fn($f) => $f->vehicles()->count()) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Usuarios de la Empresa</h5>
            </div>
            <div class="card-body">
                @if($company->users()->count())
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-info">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No hay usuarios en esta empresa</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
