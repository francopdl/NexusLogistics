@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => '', 'value' => '', 'required' => false])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label ?? ucfirst($name) }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <input 
        type="{{ $type }}"
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        @if($required) required @endif
    >
    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
