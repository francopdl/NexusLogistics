@props(['name', 'label' => '', 'options' => [], 'selected' => null, 'required' => false])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label ?? ucfirst($name) }}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    <select 
        class="form-select @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        @if($required) required @endif
    >
        <option value="">Seleccionar...</option>
        @foreach($options as $value => $label)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
