@extends('layouts.app')

@section('title', 'Entregas - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-box"></i> Gestión de Entregas</h1>
    </div>
    <div class="col-md-4 text-end">
        <x-button type="primary" href="{{ route('deliveries.create') }}">
            <i class="fas fa-plus"></i> Nueva Entrega
        </x-button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Listado de Entregas</h5>
    </div>
    <div class="card-body">
        @if($deliveries->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Información del Paquete</th>
                            <th>Ruta</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Ubicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                            <tr>
                                <td>{{ strlen($delivery->package_info) > 30 ? substr($delivery->package_info, 0, 30) . '...' : $delivery->package_info }}</td>
                                <td>
                                    <a href="{{ route('routes.show', $delivery->route) }}">
                                        {{ $delivery->route->origin }} → {{ $delivery->route->destination }}
                                    </a>
                                </td>
                                <td><a href="{{ route('clients.show', $delivery->client) }}">{{ $delivery->client->name }}</a></td>
                                <td><x-status-badge :status="$delivery->status" type="delivery" /></td>
                                <td>
                                    @if($delivery->latitude && $delivery->longitude)
                                        <span class="badge bg-info">
                                            <i class="fas fa-map-marker-alt"></i> Geo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin ubicación</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($deliveries instanceof \Illuminate\Pagination\Paginator)
                {{ $deliveries->links() }}
            @endif
        @else
            <p class="text-muted text-center mt-4">No hay entregas registradas</p>
        @endif
    </div>
</div>
@endsection
