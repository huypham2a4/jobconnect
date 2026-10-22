@props([
    'type' => 'info', // success, error, warning, info
    'message' => null,
    'dismissible' => true,
])

@php
    $typeClasses = [
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
        'error' => 'bg-rose-50 text-rose-800 border-rose-300',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-300',
        'info' => 'bg-blue-50 text-blue-800 border-blue-300',
    ];

    $icons = [
        'success' => 'fa-solid fa-circle-check',
        'error' => 'fa-solid fa-circle-xmark',
        'warning' => 'fa-solid fa-triangle-exclamation',
        'info' => 'fa-solid fa-circle-info',
    ];

    $icon = $icons[$type] ?? $icons['info'];
@endphp

<div {{ $attributes->merge(['class' => 'alert alert-' . $type]) }} 
     style="padding: 12px 18px; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; font-size: 0.92rem; background: var(--bg-card); color: var(--text-main);">
    <div style="display: flex; align-items: center; gap: 10px;">
        <i class="{{ $icon }}" style="color: var(--primary);"></i>
        <span>{{ $message ?? $slot }}</span>
    </div>
    @if($dismissible)
        <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: var(--text-muted);">&times;</button>
    @endif
</div>
