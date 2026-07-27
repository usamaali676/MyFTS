<div class="col-md-6">
    <div class="form-floating form-floating-outline">
        <input
            type="{{ $type ?? 'text' }}"
            class="form-control"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $label }}"
            @if(($type ?? 'text') === 'number') min="0" step="0.01" @endif
            @isset($max) max="{{ $max }}" @endisset
        >
        <label for="{{ $name }}">{{ $label }}</label>
    </div>
</div>
