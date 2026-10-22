@props([
    'type' => 'text',
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'icon' => null,
])

<div class="form-group" style="margin-bottom: 20px;">
    @if($label)
        <label for="{{ $name }}" style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 8px;">
            {{ $label }}
            @if($required) <span style="color: #ef4444;">*</span> @endif
        </label>
    @endif

    <div style="position: relative;">
        @if($icon)
            <i class="{{ $icon }}" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none;"></i>
        @endif

        <input type="{{ $type }}" 
               name="{{ $name }}" 
               id="{{ $name }}" 
               value="{{ old($name, $value) }}"
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               {{ $attributes->merge([
                   'style' => 'width: 100%; padding: 12px 16px ' . ($icon ? '12px 42px' : '12px 16px') . '; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-size: 0.95rem; background: var(--bg-card); color: var(--text-main); outline: none; transition: var(--transition);'
               ]) }}
        >
    </div>

    @error($name)
        <span class="text-danger" style="color: #ef4444; font-size: 0.84rem; display: block; margin-top: 6px;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right: 4px;"></i> {{ $message }}
        </span>
    @enderror
</div>
