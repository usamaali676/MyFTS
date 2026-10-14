@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/profile-enhance.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/settings-enhance.css') }}" />
    <style>
        /* Profile hero + photo. Kept inline (not in profile-enhance.css) so the
           photo block can never lose its styling if that file is stale/cached. */
        .profile-hero { overflow: hidden; }
        .profile-hero-cover {
            height: 104px;
            background:
                radial-gradient(60% 140% at 100% 0%, rgba(40, 199, 250, 0.45), rgba(40, 199, 250, 0) 70%),
                linear-gradient(120deg, #5f5aec, #8b7bff 60%, #a58bff);
        }
        .profile-hero-body {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 0 1.5rem;
            padding: 0 1.75rem 1.5rem;
        }
        .profile-hero-info { flex: 1; min-width: 220px; padding-top: 0.9rem; }

        .profile-avatar-wrap {
            position: relative;
            width: 116px;
            height: 116px;
            margin-top: -58px;
            flex-shrink: 0;
        }
        .profile-avatar-img,
        .profile-avatar-initials {
            width: 116px;
            height: 116px;
            border-radius: 50%;
            border: 4px solid var(--bs-card-bg, #fff);
            box-shadow: 0 0.5rem 1.4rem rgba(95, 90, 236, 0.35);
        }
        .profile-avatar-img { display: block; object-fit: cover; background: #f1f0ff; }
        .profile-avatar-img.d-none, .profile-avatar-initials.d-none { display: none !important; }
        .profile-avatar-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.4rem;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fff;
            background: linear-gradient(135deg, #7a7fff, #5f5aec);
        }
        .profile-avatar-trigger {
            position: absolute;
            inset: 0;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: transparent;
            cursor: pointer;
        }
        .profile-avatar-overlay {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            color: #fff;
            background: rgba(45, 40, 120, 0.58);
            opacity: 0;
            transition: opacity 0.18s ease;
        }
        .profile-avatar-overlay i { font-size: 1.6rem; line-height: 1; }
        .profile-avatar-overlay small { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .profile-avatar-trigger:hover .profile-avatar-overlay,
        .profile-avatar-trigger:focus-visible .profile-avatar-overlay { opacity: 1; }
        .profile-avatar-trigger:focus-visible { outline: 3px solid rgba(95, 90, 236, 0.5); outline-offset: 3px; }
        .profile-avatar-badge {
            position: absolute;
            right: 2px;
            bottom: 2px;
            width: 34px;
            height: 34px;
            border: 3px solid var(--bs-card-bg, #fff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: #fff;
            background: linear-gradient(135deg, #7a7fff, #5f5aec);
            box-shadow: 0 0.25rem 0.6rem rgba(95, 90, 236, 0.45);
            transition: transform 0.15s ease;
        }
        .profile-avatar-trigger:hover .profile-avatar-badge { transform: scale(1.12) rotate(-6deg); }

        /* Photo modal */
        .avatar-dropzone {
            border: 2px dashed rgba(95, 90, 236, 0.4);
            border-radius: 1rem;
            padding: 2rem 1rem;
            text-align: center;
            background: rgba(105, 108, 255, 0.04);
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }
        .avatar-dropzone:hover,
        .avatar-dropzone:focus-visible,
        .avatar-dropzone.is-dragover {
            outline: none;
            border-color: #5f5aec;
            background: rgba(105, 108, 255, 0.1);
        }
        .avatar-dropzone.is-dragover { transform: scale(1.015); }
        .avatar-dropzone.has-file { cursor: default; padding: 1.5rem 1rem; }
        .avatar-dropzone-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 0.75rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            color: #fff;
            background: linear-gradient(135deg, #7a7fff, #5f5aec);
            box-shadow: 0 0.5rem 1.1rem rgba(95, 90, 236, 0.35);
            transition: transform 0.2s ease;
        }
        .avatar-dropzone:hover .avatar-dropzone-icon,
        .avatar-dropzone.is-dragover .avatar-dropzone-icon { transform: translateY(-3px) scale(1.06); }
        .avatar-preview-ring {
            display: inline-block;
            padding: 4px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7a7fff, #28c7fa);
            box-shadow: 0 0.5rem 1.4rem rgba(95, 90, 236, 0.3);
        }
        .avatar-preview-ring img {
            display: block;
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--bs-card-bg, #fff);
        }

        @media (max-width: 575.98px) {
            .profile-hero-body { justify-content: center; text-align: center; }
            .profile-hero-info { padding-top: 0.75rem; }
        }
    </style>
@endsection
@section('content')
    @php
        $initials = collect(explode(' ', trim($target->name)))
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
        $initials = strtoupper($initials) ?: '?';

        $rankLabel = function (?array $rank) {
            if (!$rank || !$rank['position']) {
                return 'Not ranked this period';
            }
            return "#{$rank['position']} of {$rank['of']}";
        };
    @endphp
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y profile-page">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Profile /</span> {{ $isSelf ? 'My Profile' : $target->name }}
            </h4>

            @unless($isSelf)
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="mdi mdi-eye-outline me-2"></i>
                    <span>Viewing <strong>{{ $target->name }}</strong>'s profile.</span>
                    <a href="{{ route('user.index') }}" class="ms-auto">Back to Users</a>
                </div>
            @endunless

            <!-- Identity card -->
            <div class="card mb-4 profile-hero">
                <div class="profile-hero-cover"></div>
                <div class="profile-hero-body">
                    <div class="profile-avatar-wrap">
                        <img id="profileAvatarImg" class="profile-avatar-img {{ $target->avatar_url ? '' : 'd-none' }}"
                            src="{{ $target->avatar_url }}" alt="{{ $target->name }}" />
                        <div id="profileAvatarInitials" class="profile-avatar-initials {{ $target->avatar_url ? 'd-none' : '' }}">{{ $initials }}</div>
                        @if($isSelf)
                            <button type="button" class="profile-avatar-trigger" data-bs-toggle="modal" data-bs-target="#avatarModal"
                                title="Change profile photo" aria-label="Change profile photo">
                                <span class="profile-avatar-overlay"><i class="mdi mdi-camera-plus-outline"></i><small>Change</small></span>
                                <span class="profile-avatar-badge"><i class="mdi mdi-camera"></i></span>
                            </button>
                        @endif
                    </div>
                    <div class="profile-hero-info">
                        <h4 class="mb-1">{{ $target->name }}</h4>
                        <p class="text-muted mb-2"><i class="mdi mdi-email-outline me-1"></i>{{ $target->email }}</p>
                        <span class="badge bg-label-primary me-2">{{ optional($target->role)->name ?? 'N/A' }}</span>
                        <span class="badge {{ $target->status ? 'bg-label-success' : 'bg-label-secondary' }} me-2">
                            {{ $target->status ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-muted small"><i class="mdi mdi-calendar-outline me-1"></i>Joined {{ \Carbon\Carbon::parse($target->created_at)->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Range filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('profile.show', $isSelf ? null : $target->id) }}"
                        class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Time Range</label>
                            <select name="range" id="profile_range_select" class="form-select">
                                <option value="all" {{ $rangeKey == 'all' ? 'selected' : '' }}>All Time</option>
                                <option value="this_month" {{ $rangeKey == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ $rangeKey == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="custom" {{ $rangeKey == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 profile-custom-range-field" {{ $rangeKey == 'custom' ? '' : 'style=display:none' }}>
                            <label class="form-label">From</label>
                            <input type="date" name="from" class="form-control" value="{{ $customFrom }}">
                        </div>
                        <div class="col-md-3 profile-custom-range-field" {{ $rangeKey == 'custom' ? '' : 'style=display:none' }}>
                            <label class="form-label">To</label>
                            <input type="date" name="to" class="form-control" value="{{ $customTo }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                    </form>
                    <div class="text-muted small mt-2">Showing: {{ $range['label'] }}</div>
                </div>
            </div>

            @if($attendance)
                <h6 class="mb-3">Attendance & Breaks</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-calendar-check-outline', 'label' => 'Shifts', 'value' => $attendance['shifts'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-clock-alert-outline', 'label' => 'Late Arrivals', 'value' => $attendance['lates'], 'color' => 'bg-label-warning'],
                            ['icon' => 'mdi-calendar-remove-outline', 'label' => 'Absents', 'value' => $attendance['absents'], 'color' => 'bg-label-danger'],
                            ['icon' => 'mdi-coffee-outline', 'label' => 'Breaks Taken', 'value' => $attendance['break_count'], 'sub' => $attendance['avg_breaks_per_shift'] . ' avg / shift', 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-timer-sand', 'label' => 'Avg Break Duration / Day', 'value' => \App\Helpers\GlobalHelper::formatSeconds($attendance['avg_break_seconds_per_day']), 'color' => 'bg-label-secondary'],
                            ['icon' => 'mdi-timer-outline', 'label' => 'Total Break Time', 'value' => \App\Helpers\GlobalHelper::formatSeconds($attendance['total_break_seconds']), 'color' => 'bg-label-secondary'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($closer)
                <h6 class="mb-3">Closer Performance</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Sales Closed', 'value' => $closer['sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Revenue', 'value' => '$' . number_format($closer['revenue'], 2), 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-chart-line', 'label' => 'Avg Deal Size', 'value' => '$' . number_format($closer['avg_deal'], 2), 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-trophy-outline', 'label' => 'Rank vs Closers', 'value' => $rankLabel($closer['rank']), 'color' => 'bg-label-warning'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($customerSupport)
                <h6 class="mb-3">Customer Support Performance</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-account-heart-outline', 'label' => 'Sales Managed', 'value' => $customerSupport['sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Revenue', 'value' => '$' . number_format($customerSupport['revenue'], 2), 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-chart-line', 'label' => 'Avg Deal Size', 'value' => '$' . number_format($customerSupport['avg_deal'], 2), 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-trophy-outline', 'label' => 'Rank vs CS Reps', 'value' => $rankLabel($customerSupport['rank']), 'color' => 'bg-label-warning'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($salesRep)
                <h6 class="mb-3">Leads & Conversions</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-account-plus-outline', 'label' => 'Leads Created', 'value' => $salesRep['leads'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Converted to Sale', 'value' => $salesRep['converted'], 'sub' => $salesRep['conversion_rate'] . '% conversion', 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Revenue', 'value' => '$' . number_format($salesRep['revenue'], 2), 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-trophy-outline', 'label' => 'Rank vs Peers', 'value' => $rankLabel($salesRep['rank']), 'color' => 'bg-label-warning'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

            @if($team)
                <h6 class="mb-3">Team Overview (Customer Support)</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-account-group-outline', 'label' => 'Team Sales', 'value' => $team['total_sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Team Revenue', 'value' => '$' . number_format($team['total_revenue'], 2), 'color' => 'bg-label-success'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
                @if($team['reps']->isNotEmpty())
                    <div class="card mb-4">
                        <h6 class="card-header">By Rep</h6>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Rep</th>
                                        <th>Sales</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($team['reps'] as $rep)
                                        <tr>
                                            <td>{{ $rep['user']->name }}</td>
                                            <td>{{ $rep['sales'] }}</td>
                                            <td>${{ number_format($rep['revenue'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            @if($company)
                <h6 class="mb-3">Company Overview</h6>
                <div class="row g-4 mb-4">
                    @php
                        $cards = [
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Total Sales', 'value' => $company['total_sales'], 'color' => 'bg-label-primary'],
                            ['icon' => 'mdi-currency-usd', 'label' => 'Total Revenue', 'value' => '$' . number_format($company['total_revenue'], 2), 'color' => 'bg-label-success'],
                            ['icon' => 'mdi-account-voice', 'label' => 'Active TSRs', 'value' => $company['tsr_count'], 'color' => 'bg-label-info'],
                            ['icon' => 'mdi-handshake-outline', 'label' => 'Active Closers', 'value' => $company['closer_count'], 'color' => 'bg-label-warning'],
                            ['icon' => 'mdi-account-heart-outline', 'label' => 'Active CSRs', 'value' => $company['csr_count'], 'color' => 'bg-label-secondary'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                        @include('pages.profile.partials.stat-card', $card)
                    @endforeach
                </div>
            @endif

        </div>
        <div class="content-backdrop fade"></div>
    </div>

    @if($isSelf)
    <!-- Profile photo modal -->
    <div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content settings-modal">
                <button type="button" class="btn-close settings-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-header settings-modal-header">
                    <div class="settings-modal-icon"><i class="mdi mdi-camera"></i></div>
                    <div>
                        <h5 class="modal-title mb-0">Profile Photo</h5>
                        <p class="settings-modal-subtitle mb-0">Any photo works &mdash; we crop and optimize it for you</p>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="avatarDropzone" class="avatar-dropzone" tabindex="0" role="button">
                        <div class="avatar-dropzone-empty" id="avatarDropzoneEmpty">
                            <span class="avatar-dropzone-icon"><i class="mdi mdi-cloud-upload-outline"></i></span>
                            <div class="fw-semibold">Drag &amp; drop a photo here</div>
                            <div class="text-muted small mb-2">or click to browse</div>
                            <span class="badge bg-label-primary">JPG</span> <span class="badge bg-label-primary">PNG</span> <span class="badge bg-label-primary">WebP</span> <span class="badge bg-label-primary">GIF</span>
                        </div>
                        <div class="avatar-dropzone-preview d-none" id="avatarDropzonePreview">
                            <span class="avatar-preview-ring"><img id="avatarPreviewImg" alt="Preview" /></span>
                            <div class="text-muted small mt-3" id="avatarPreviewMeta"></div>
                            <div class="small mt-1"><a href="javascript:void(0)" id="avatarChooseAnother">Choose a different photo</a></div>
                        </div>
                    </div>
                    <input type="file" id="avatarFileInput" accept="image/*" class="d-none" />
                    <div class="alert alert-danger py-2 px-3 mt-3 mb-0 d-none" id="avatarError"></div>
                    <div class="text-muted small mt-3 text-center">
                        <i class="mdi mdi-auto-fix"></i> It's cropped to a square and compressed to under 100&nbsp;KB automatically.
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-text-danger {{ $target->avatar_url ? '' : 'd-none' }}" id="avatarRemoveBtn">
                        <i class="mdi mdi-delete-outline me-1"></i> Remove photo
                    </button>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary settings-modal-save" id="avatarSaveBtn" disabled>
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="avatarSaveSpinner"></span>
                            <span id="avatarSaveLabel">Save photo</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
@section('js')
    @if($isSelf)
    <script>
        (function () {
            var urls = {
                update: @json(route('profile.avatar.update')),
                remove: @json(route('profile.avatar.remove'))
            };
            var csrf = document.querySelector('meta[name="csrf-token"]').content;

            var dropzone = document.getElementById('avatarDropzone');
            var fileInput = document.getElementById('avatarFileInput');
            var emptyState = document.getElementById('avatarDropzoneEmpty');
            var previewState = document.getElementById('avatarDropzonePreview');
            var previewImg = document.getElementById('avatarPreviewImg');
            var previewMeta = document.getElementById('avatarPreviewMeta');
            var saveBtn = document.getElementById('avatarSaveBtn');
            var saveLabel = document.getElementById('avatarSaveLabel');
            var saveSpinner = document.getElementById('avatarSaveSpinner');
            var removeBtn = document.getElementById('avatarRemoveBtn');
            var errorBox = document.getElementById('avatarError');
            var modalEl = document.getElementById('avatarModal');

            var chosenFile = null;
            var previewUrl = null;

            function toast(icon, title) {
                if (window.Swal) {
                    Swal.fire({ toast: true, position: 'top-end', icon: icon, title: title, showConfirmButton: false, timer: 2800, timerProgressBar: true });
                }
            }

            function showError(message) {
                errorBox.textContent = message;
                errorBox.classList.remove('d-none');
            }

            function resetModal() {
                chosenFile = null;
                fileInput.value = '';
                if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }
                emptyState.classList.remove('d-none');
                previewState.classList.add('d-none');
                dropzone.classList.remove('has-file', 'is-dragover');
                errorBox.classList.add('d-none');
                saveBtn.disabled = true;
                setBusy(false);
            }

            function setBusy(busy) {
                saveSpinner.classList.toggle('d-none', !busy);
                saveLabel.textContent = busy ? 'Optimizing & saving…' : 'Save photo';
                saveBtn.disabled = busy || !chosenFile;
                removeBtn.disabled = busy;
            }

            function formatSize(bytes) {
                return bytes > 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
            }

            function chooseFile(file) {
                errorBox.classList.add('d-none');
                if (!file) { return; }
                if (!/^image\/(jpeg|png|webp|gif)$/.test(file.type)) {
                    showError('Please choose a JPG, PNG, WebP or GIF image.');
                    return;
                }
                chosenFile = file;
                if (previewUrl) { URL.revokeObjectURL(previewUrl); }
                previewUrl = URL.createObjectURL(file);
                previewImg.src = previewUrl;
                previewMeta.textContent = file.name + ' · ' + formatSize(file.size);
                emptyState.classList.add('d-none');
                previewState.classList.remove('d-none');
                dropzone.classList.add('has-file');
                saveBtn.disabled = false;
            }

            dropzone.addEventListener('click', function (e) {
                if (e.target.id === 'avatarChooseAnother' || !chosenFile) { fileInput.click(); }
            });
            dropzone.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInput.click(); }
            });
            fileInput.addEventListener('change', function () { chooseFile(fileInput.files[0]); });

            ['dragenter', 'dragover'].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) { e.preventDefault(); dropzone.classList.add('is-dragover'); });
            });
            ['dragleave', 'drop'].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) { e.preventDefault(); dropzone.classList.remove('is-dragover'); });
            });
            dropzone.addEventListener('drop', function (e) { chooseFile(e.dataTransfer.files[0]); });

            modalEl.addEventListener('hidden.bs.modal', resetModal);

            // Big phone photos are shrunk in the browser first so they upload
            // fast; the server still does the real crop + 100 KB compression.
            function prepareForUpload(file) {
                if (file.size < 1500000 || !window.createImageBitmap) { return Promise.resolve(file); }
                return createImageBitmap(file, { imageOrientation: 'from-image' }).then(function (bitmap) {
                    var scale = Math.min(1, 1600 / Math.max(bitmap.width, bitmap.height));
                    var canvas = document.createElement('canvas');
                    canvas.width = Math.round(bitmap.width * scale);
                    canvas.height = Math.round(bitmap.height * scale);
                    var ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#fff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
                    return new Promise(function (resolve) {
                        canvas.toBlob(function (blob) { resolve(blob || file); }, 'image/jpeg', 0.9);
                    });
                }).catch(function () { return file; });
            }

            function applyAvatar(url) {
                var img = document.getElementById('profileAvatarImg');
                var initials = document.getElementById('profileAvatarInitials');
                if (url) {
                    var busted = url + (url.indexOf('?') === -1 ? '?v=' : '&v=') + Date.now();
                    img.src = busted;
                    img.classList.remove('d-none');
                    initials.classList.add('d-none');
                    document.querySelectorAll('.js-user-avatar').forEach(function (el) { el.src = busted; });
                    removeBtn.classList.remove('d-none');
                } else {
                    img.classList.add('d-none');
                    initials.classList.remove('d-none');
                    document.querySelectorAll('.js-user-avatar').forEach(function (el) { el.src = @json(asset('assets/img/avatars/1.png')); });
                    removeBtn.classList.add('d-none');
                }
            }

            saveBtn.addEventListener('click', function () {
                if (!chosenFile) { return; }
                errorBox.classList.add('d-none');
                setBusy(true);

                prepareForUpload(chosenFile).then(function (payload) {
                    var form = new FormData();
                    form.append('photo', payload, chosenFile.name.replace(/\.\w+$/, '') + (payload === chosenFile ? '' : '.jpg'));
                    return fetch(urls.update, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: form
                    });
                }).then(function (res) {
                    return res.json().catch(function () { return {}; }).then(function (data) { return { ok: res.ok, data: data }; });
                }).then(function (result) {
                    if (!result.ok) {
                        var errs = result.data.errors ? Object.values(result.data.errors)[0][0] : null;
                        throw new Error(errs || result.data.message || 'Upload failed. Please try again.');
                    }
                    applyAvatar(result.data.url);
                    bootstrap.Modal.getInstance(modalEl).hide();
                    toast('success', 'Profile photo updated');
                }).catch(function (err) {
                    setBusy(false);
                    showError(err.message || 'Upload failed. Please try again.');
                });
            });

            removeBtn.addEventListener('click', function () {
                var run = function () {
                    setBusy(true);
                    fetch(urls.remove, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } })
                        .then(function (res) {
                            if (!res.ok) { throw new Error('Could not remove the photo.'); }
                            applyAvatar(null);
                            bootstrap.Modal.getInstance(modalEl).hide();
                            toast('success', 'Profile photo removed');
                        })
                        .catch(function (err) { setBusy(false); showError(err.message); });
                };
                if (window.Swal) {
                    Swal.fire({ title: 'Remove your photo?', text: 'Your initials will be shown instead.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Remove', didOpen: function () { document.querySelector('.swal2-container').style.zIndex = 2000; }, customClass: { confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-outline-secondary' }, buttonsStyling: false })
                        .then(function (r) { if (r.isConfirmed) { run(); } });
                } else if (confirm('Remove your photo?')) { run(); }
            });
        })();
    </script>
    @endif
    <script>
        document.getElementById('profile_range_select').addEventListener('change', function () {
            var isCustom = this.value === 'custom';
            document.querySelectorAll('.profile-custom-range-field').forEach(function (el) {
                el.style.display = isCustom ? '' : 'none';
            });
        });
    </script>
@endsection
