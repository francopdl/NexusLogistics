@extends('layouts.app')

@section('title', 'Empresas - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-building"></i> Gestión de Empresas</h1>
    </div>
    <div class="col-md-4 text-end">
        <x-button type="primary" href="{{ route('companies.create') }}">
            <i class="fas fa-plus"></i> Nueva Empresa
        </x-button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Listado de Empresas</h5>
    </div>
    <div class="card-body">
        @if($companies->count())
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Ciudad</th>
                        <th>País</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                        <tr>
                            <td><strong>{{ $company->name }}</strong></td>
                            <td>{{ $company->email }}</td>
                            <td>{{ $company->phone }}</td>
                            <td>{{ $company->city }}</td>
                            <td>{{ $company->country }}</td>
                            <td>
                                <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('companies.destroy', $company) }}" method="POST" style="display:inline;">
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
            
            @if($companies instanceof \Illuminate\Pagination\Paginator)
                {{ $companies->links() }}
            @endif
        @else
            <p class="text-muted text-center mt-4">No hay empresas registradas</p>
        @endif
    </div>
</div>
@endsection
