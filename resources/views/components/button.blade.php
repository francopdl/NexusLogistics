@props(['type' => 'primary', 'size' => 'md', 'href' => null])

@if($href)
    <a href="{{ $href }}" class="btn btn-{{ $type }} btn-{{ $size }}">
        {{ $slot }}
    </a>
@else
    <button type="submit" class="btn btn-{{ $type }} btn-{{ $size }}">
        {{ $slot }}
    </button>
@endif
