@props(['type' => 'info', 'message' => ''])

<div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
    <i class="fas fa-{{ match($type) {
        'success' => 'check-circle',
        'danger' => 'exclamation-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'info-circle',
        default => 'info-circle'
    } }}"></i>
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
