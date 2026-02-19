@extends('layouts.app')

@section('title', $client->name . ' - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-user"></i> {{ $client->name }}</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
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
                <p><strong>Nombre:</strong> {{ $client->name }}</p>
                <p><strong>Email:</strong> <a href="mailto:{{ $client->email }}">{{ $client->email }}</a></p>
                <p><strong>Teléfono:</strong> {{ $client->phone }}</p>
                <p><strong>Dirección:</strong> {{ $client->address }}</p>
                <p><strong>Ciudad:</strong> {{ $client->city }}</p>
                <p><strong>Empresa:</strong> <a href="{{ route('companies.show', $client->company) }}">{{ $client->company->name }}</a></p>
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
                        <h6 class="text-muted">Entregas Totales</h6>
                        <h2 class="text-primary">{{ $client->deliveries()->count() }}</h2>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Entregas Pendientes</h6>
                        <h2 class="text-warning">{{ $client->deliveries()->where('status', '!=', 'delivered')->count() }}</h2>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Entregas Completadas</h6>
                        <h2 class="text-success">{{ $client->deliveries()->where('status', 'delivered')->count() }}</h2>
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
                <h5 class="mb-0"><i class="fas fa-box"></i> Entregas del Cliente</h5>
            </div>
            <div class="card-body">
                @if($client->deliveries()->count())
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ruta</th>
                                <th>Paquete</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($client->deliveries as $delivery)
                                <tr>
                                    <td>#{{ $delivery->id }}</td>
                                    <td>{{ $delivery->route->origin }} → {{ $delivery->route->destination }}</td>
                                    <td>{{ substr($delivery->package_info, 0, 30) }}...</td>
                                    <td><x-status-badge :status="$delivery->status" type="delivery" /></td>
                                    <td>{{ $delivery->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No hay entregas para este cliente</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
