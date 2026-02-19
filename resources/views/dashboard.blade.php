@extends('layouts.app')

@section('title', 'Dashboard - Nexus Logistics')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1><i class="fas fa-home"></i> Dashboard</h1>
        <p class="text-muted">Bienvenido a Nexus Logistics System</p>
    </div>
</div>

<div class="row mt-4">
    <!-- Total Companies -->
    <div class="col-md-3 mb-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">Empresas</h5>
                <h2 class="text-primary">{{ $totalCompanies ?? 0 }}</h2>
                <p class="card-text text-muted">Total de empresas registradas</p>
            </div>
        </div>
    </div>

    <!-- Total Clients -->
    <div class="col-md-3 mb-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">Clientes</h5>
                <h2 class="text-success">{{ $totalClients ?? 0 }}</h2>
                <p class="card-text text-muted">Total de clientes</p>
            </div>
        </div>
    </div>

    <!-- Total Vehicles -->
    <div class="col-md-3 mb-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">Vehículos</h5>
                <h2 class="text-warning">{{ $totalVehicles ?? 0 }}</h2>
                <p class="card-text text-muted">Total de vehículos</p>
            </div>
        </div>
    </div>

    <!-- Pending Deliveries -->
    <div class="col-md-3 mb-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">Entregas Pendientes</h5>
                <h2 class="text-danger">{{ $pendingDeliveries ?? 0 }}</h2>
                <p class="card-text text-muted">Entregas sin completar</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Deliveries -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> Entregas Recientes</h5>
            </div>
            <div class="card-body">
                @if(isset($recentDeliveries) && $recentDeliveries->count())
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDeliveries as $delivery)
                                <tr>
                                    <td>#{{ $delivery->id }}</td>
                                    <td>{{ $delivery->client->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ match($delivery->status) {
                                            'delivered' => 'success',
                                            'in_transit' => 'info',
                                            'failed' => 'danger',
                                            default => 'warning'
                                        } }}">
                                            {{ ucfirst($delivery->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No hay entregas recientes</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Routes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map"></i> Rutas Activas</h5>
            </div>
            <div class="card-body">
                @if(isset($activeRoutes) && $activeRoutes->count())
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeRoutes as $route)
                                <tr>
                                    <td>#{{ $route->id }}</td>
                                    <td>{{ substr($route->origin, 0, 20) }}...</td>
                                    <td>{{ substr($route->destination, 0, 20) }}...</td>
                                    <td>
                                        <span class="badge bg-{{ match($route->status) {
                                            'completed' => 'success',
                                            'in_progress' => 'info',
                                            'cancelled' => 'danger',
                                            default => 'warning'
                                        } }}">
                                            {{ ucfirst(str_replace('_', ' ', $route->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No hay rutas activas</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
