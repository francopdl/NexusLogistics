@extends('layouts.app')

@section('title', 'Flotas - Nexus Logistics')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="fas fa-boxes"></i> Gestión de Flotas</h1>
    </div>
    <div class="col-md-4 text-end">
        <x-button type="primary" href="{{ route('fleets.create') }}">
            <i class="fas fa-plus"></i> Nueva Flota
        </x-button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Listado de Flotas</h5>
    </div>
    <div class="card-body">
        @if($fleets->count())
            <div class="row">
                @foreach($fleets as $fleet)
                    <div class="col-md-6 mb-3">
                        <div class="card border-left border-primary">
                            <div class="card-body">
                                <h5 class="card-title">{{ $fleet->name }}</h5>
                                <p class="card-text text-muted">{{ $fleet->company->name }}</p>
                                <p class="card-text small">{{ $fleet->description }}</p>
                                <div class="mt-3">
                                    <span class="badge bg-info">{{ $fleet->vehicles()->count() }} Vehículos</span>
                                    <span class="badge bg-secondary">{{ $fleet->routes()->count() }} Rutas</span>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('fleets.show', $fleet) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('fleets.edit', $fleet) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('fleets.destroy', $fleet) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($fleets instanceof \Illuminate\Pagination\Paginator)
                {{ $fleets->links() }}
            @endif
        @else
            <p class="text-muted text-center mt-4">No hay flotas registradas</p>
        @endif
    </div>
</div>
@endsection
