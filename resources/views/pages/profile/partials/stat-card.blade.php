{{-- Expects: $icon, $label, $value, $color (bg-label-*), and optional $sub --}}
<div class="col-sm-6 col-lg-3">
    <div class="card h-100 profile-stat-card">
        <div class="card-body d-flex justify-content-between align-items-start">
            <div>
                <p class="mb-1 text-muted">{{ $label }}</p>
                <h4 class="mb-0">{{ $value }}</h4>
                @isset($sub)
                    <p class="mb-0 text-muted small">{{ $sub }}</p>
                @endisset
            </div>
            <div class="avatar">
                <span class="avatar-initial rounded {{ $color ?? 'bg-label-primary' }}">
                    <i class="mdi {{ $icon }} mdi-24px"></i>
                </span>
            </div>
        </div>
    </div>
</div>
