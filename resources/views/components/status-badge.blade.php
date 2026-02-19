@props(['status', 'type' => 'delivery'])

@php
    $statuses = match($type) {
        'delivery' => [
            'pending' => ['label' => 'Pendiente', 'color' => 'warning'],
            'in_transit' => ['label' => 'En Tránsito', 'color' => 'info'],
            'delivered' => ['label' => 'Entregada', 'color' => 'success'],
            'failed' => ['label' => 'Fallida', 'color' => 'danger'],
        ],
        'route' => [
            'pending' => ['label' => 'Pendiente', 'color' => 'warning'],
            'in_progress' => ['label' => 'En Progreso', 'color' => 'info'],
            'completed' => ['label' => 'Completada', 'color' => 'success'],
            'cancelled' => ['label' => 'Cancelada', 'color' => 'danger'],
        ],
        'vehicle' => [
            'available' => ['label' => 'Disponible', 'color' => 'success'],
            'in_use' => ['label' => 'En Uso', 'color' => 'info'],
            'maintenance' => ['label' => 'Mantenimiento', 'color' => 'warning'],
        ],
        default => []
    };
    
    $statusInfo = $statuses[$status] ?? ['label' => ucfirst($status), 'color' => 'secondary'];
@endphp

<span class="badge bg-{{ $statusInfo['color'] }}">
    {{ $statusInfo['label'] }}
</span>
