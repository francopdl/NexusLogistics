@props(['name', 'label' => '', 'placeholder' => '', 'value' => '', 'required' => false, 'rows' => 4])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label ?? ucfirst($name) }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <textarea 
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @if($required) required @endif
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
