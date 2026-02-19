@extends('layouts.app')

@section('title', 'Clientes - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-users"></i> Gestión de Clientes</h1>
    </div>
    <div class="col-md-4 text-end">
        <x-button type="primary" href="{{ route('clients.create') }}">
            <i class="fas fa-plus"></i> Nuevo Cliente
        </x-button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Listado de Clientes</h5>
    </div>
    <div class="card-body">
        @if($clients->count())
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Ciudad</th>
                        <th>Empresa</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td><strong>{{ $client->name }}</strong></td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->city }}</td>
                            <td>{{ $client->company->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display:inline;">
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
            
            @if($clients instanceof \Illuminate\Pagination\Paginator)
                {{ $clients->links() }}
            @endif
        @else
            <p class="text-muted text-center mt-4">No hay clientes registrados</p>
        @endif
    </div>
</div>
@endsection
