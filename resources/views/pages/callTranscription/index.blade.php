@extends('layouts.dashboard')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
<style>
    .ct-shell { max-width: 900px; margin: 0 auto; }

    /* Upload zone */
    .ct-dropzone {
        border: 2px dashed rgba(46, 38, 61, .25);
        border-radius: .75rem;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s, transform .15s;
        background: #fff;
    }
    .ct-dropzone:hover { border-color: var(--bs-primary); background: rgba(102, 108, 255, .04); }
    .ct-dropzone.dragover { border-color: var(--bs-primary); background: rgba(102, 108, 255, .08); transform: scale(1.01); }
    .ct-dropzone.has-error { border-color: #ff4d49; animation: ctShake .4s; }
    @keyframes ctShake {
        10%, 90% { transform: translateX(-1px); }
        20%, 80% { transform: translateX(2px); }
        30%, 50%, 70% { transform: translateX(-4px); }
        40%, 60% { transform: translateX(4px); }
    }
    .ct-dropzone .ct-icon {
        width: 64px; height: 64px; border-radius: 50%;
        background: rgba(102, 108, 255, .1);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
    }
    .ct-file-chip {
        display: none;
        align-items: center; gap: .5rem;
        background: rgba(40, 199, 111, .1);
        border: 1px solid rgba(40, 199, 111, .25);
        border-radius: .5rem;
        padding: .5rem .75rem;
        margin-top: 1rem;
        font-size: .85rem;
    }
    .ct-file-chip.show { display: inline-flex; }

    /* Processing */
    .ct-processing { display: none; text-align: center; padding: 3rem 1rem; }
    .ct-processing.show { display: block; }
    .ct-waveform { display: flex; align-items: flex-end; justify-content: center; gap: 4px; height: 60px; margin-bottom: 1.5rem; }
    .ct-waveform span {
        width: 6px; border-radius: 3px; background: var(--bs-primary);
        animation: ctBar 1.2s ease-in-out infinite;
    }
    .ct-waveform span:nth-child(1) { height: 20%; animation-delay: 0s; }
    .ct-waveform span:nth-child(2) { height: 60%; animation-delay: .1s; }
    .ct-waveform span:nth-child(3) { height: 100%; animation-delay: .2s; }
    .ct-waveform span:nth-child(4) { height: 40%; animation-delay: .3s; }
    .ct-waveform span:nth-child(5) { height: 80%; animation-delay: .4s; }
    .ct-waveform span:nth-child(6) { height: 30%; animation-delay: .5s; }
    .ct-waveform span:nth-child(7) { height: 70%; animation-delay: .6s; }
    @keyframes ctBar {
        0%, 100% { transform: scaleY(.3); opacity: .5; }
        50% { transform: scaleY(1); opacity: 1; }
    }
    .ct-status-text { font-size: .95rem; color: #4b4b4b; min-height: 1.4em; }

    /* Result */
    .ct-result { display: none; }
    .ct-result.show { display: block; animation: ctFadeIn .35s ease both; }
    @keyframes ctFadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .ct-meta-strip { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; }
    .ct-meta-strip .badge { font-size: .75rem; }
    .ct-turn {
        display: flex; gap: .75rem; padding: .75rem 0;
        border-bottom: 1px solid rgba(46, 38, 61, .08);
        opacity: 0; animation: ctTurnIn .3s ease forwards;
    }
    @keyframes ctTurnIn { to { opacity: 1; } }
    .ct-turn .ct-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: .8rem; font-weight: 600; color: #fff;
    }
    .ct-turn.agent .ct-avatar { background: var(--bs-primary); }
    .ct-turn.client .ct-avatar { background: #a1acb8; }
    .ct-turn .ct-ts { font-size: .7rem; color: #a1acb8; }
    .ct-turn .ct-speaker { font-weight: 600; font-size: .85rem; }
    .ct-turn .ct-text { font-size: .9rem; color: #4b4b4b; margin-top: 2px; white-space: pre-wrap; }
    .ct-copy-flash { color: #28c76f !important; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ct-shell">
    <h4 class="mb-4">
        <i class="mdi mdi-microphone-message-outline text-primary me-1"></i>
        Call Transcription
    </h4>

    {{-- ══════ Stage 1: Upload ══════ --}}
    <div id="ctUploadStage">
        <div class="card mb-3">
            <div class="card-body">
                <div id="ctDropzone" class="ct-dropzone">
                    <div class="ct-icon">
                        <i class="mdi mdi-cloud-upload-outline mdi-32px text-primary"></i>
                    </div>
                    <h6 class="mb-1">Drag & drop a call recording here</h6>
                    <p class="text-muted mb-2" style="font-size:.85rem;">or click to browse &middot; MP3, WAV, M4A, AAC, OGG, WEBM &middot; up to 24 MB</p>
                    <input type="file" id="ctFileInput" accept=".mp3,.wav,.m4a,.aac,.ogg,.webm,audio/*" class="d-none">
                    <div id="ctFileChip" class="ct-file-chip">
                        <i class="mdi mdi-file-music-outline"></i>
                        <span id="ctFileName"></span>
                        <button type="button" id="ctFileClear" class="btn btn-sm btn-icon" title="Remove"><i class="mdi mdi-close"></i></button>
                    </div>
                </div>
                <div id="ctFileError" class="text-danger mt-2" style="display:none; font-size:.85rem;"></div>

                <div class="mt-4">
                    <label class="form-label" for="ctAgentSelect">Agent on this call</label>
                    <select id="ctAgentSelect" class="form-select" style="width:100%;">
                        <option value="">Search for an agent…</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button id="ctGenerateBtn" class="btn btn-primary mt-4" disabled>
                    <i class="mdi mdi-auto-fix me-1"></i> Generate Transcript
                </button>
            </div>
        </div>
    </div>

    {{-- ══════ Stage 2: Processing ══════ --}}
    <div id="ctProcessingStage" class="card ct-processing">
        <div class="card-body">
            <div class="ct-waveform">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
            <div id="ctUploadProgressWrap" class="mb-3" style="max-width:320px; margin:0 auto;">
                <div class="progress" style="height:6px;">
                    <div id="ctUploadProgressBar" class="progress-bar" style="width:0%"></div>
                </div>
            </div>
            <div id="ctStatusText" class="ct-status-text fw-medium"></div>
        </div>
    </div>

    {{-- ══════ Stage 3: Result ══════ --}}
    <div id="ctResultStage" class="ct-result">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <h6 class="mb-0" id="ctResultFilename"></h6>
                        <span class="text-muted" style="font-size:.8rem;">Agent: <span id="ctResultAgent"></span></span>
                    </div>
                    <div class="d-flex gap-2">
                        <button id="ctCopyAllBtn" class="btn btn-outline-secondary btn-sm">
                            <i class="mdi mdi-content-copy me-1"></i> Copy
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="mdi mdi-download-outline me-1"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" id="ctExportTxt" href="#">TXT</a></li>
                                <li><a class="dropdown-item" id="ctExportPdf" href="#">PDF</a></li>
                                <li><a class="dropdown-item" id="ctExportDocx" href="#">DOCX</a></li>
                            </ul>
                        </div>
                        <button id="ctNewTranscriptBtn" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus me-1"></i> New Transcript
                        </button>
                    </div>
                </div>

                <div class="ct-meta-strip">
                    <span class="badge bg-label-primary"><i class="mdi mdi-clock-outline me-1"></i><span id="ctMetaDuration"></span></span>
                    <span class="badge bg-label-secondary"><i class="mdi mdi-text-long me-1"></i><span id="ctMetaWords"></span> words</span>
                    <span class="badge bg-label-secondary"><i class="mdi mdi-forum-outline me-1"></i><span id="ctMetaExchanges"></span> exchanges</span>
                    <span class="badge bg-label-secondary"><i class="mdi mdi-timer-outline me-1"></i><span id="ctMetaProcessingTime"></span></span>
                </div>

                <div id="ctTurnsContainer"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
(function () {
    'use strict';

    const routes = {
        store: '{{ route('calltranscription.store') }}',
        exportUrl: (uuid, fmt) => '{{ url('call-transcription') }}/' + uuid + '/export/' + fmt,
    };

    const statusMessages = [
        'Uploading audio…',
        'Processing recording…',
        'Detecting speakers…',
        'Identifying agent responses…',
        'Generating transcript…',
        'Finalizing transcript…',
    ];

    let selectedFile = null;
    let statusInterval = null;
    let lastResult = null;

    const dropzone = document.getElementById('ctDropzone');
    const fileInput = document.getElementById('ctFileInput');
    const fileChip = document.getElementById('ctFileChip');
    const fileName = document.getElementById('ctFileName');
    const fileClear = document.getElementById('ctFileClear');
    const fileError = document.getElementById('ctFileError');
    const generateBtn = document.getElementById('ctGenerateBtn');
    const agentSelect = document.getElementById('ctAgentSelect');

    const uploadStage = document.getElementById('ctUploadStage');
    const processingStage = document.getElementById('ctProcessingStage');
    const resultStage = document.getElementById('ctResultStage');
    const statusText = document.getElementById('ctStatusText');
    const uploadProgressBar = document.getElementById('ctUploadProgressBar');

    const ALLOWED_EXT = ['mp3', 'wav', 'm4a', 'aac', 'ogg', 'webm'];
    const MAX_BYTES = 24 * 1024 * 1024;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function generateUuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function showFileError(message) {
        fileError.textContent = message;
        fileError.style.display = 'block';
        dropzone.classList.add('has-error');
        setTimeout(() => dropzone.classList.remove('has-error'), 400);
    }

    function clearFileError() {
        fileError.style.display = 'none';
        fileError.textContent = '';
    }

    function updateGenerateEnabled() {
        generateBtn.disabled = !(selectedFile && agentSelect.value);
    }

    function acceptFile(file) {
        clearFileError();
        const ext = file.name.split('.').pop().toLowerCase();

        if (!ALLOWED_EXT.includes(ext)) {
            showFileError('Unsupported file type. Supported formats: MP3, WAV, M4A, AAC, OGG, WEBM.');
            return;
        }
        if (file.size > MAX_BYTES) {
            showFileError('File is too large. Maximum size is 24 MB per recording.');
            return;
        }

        selectedFile = file;
        fileName.textContent = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(1) + ' MB)';
        fileChip.classList.add('show');
        updateGenerateEnabled();
    }

    dropzone.addEventListener('click', (e) => {
        if (e.target.closest('#ctFileClear')) return;
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) acceptFile(fileInput.files[0]);
    });

    ['dragenter', 'dragover'].forEach(evt => {
        dropzone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropzone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        });
    });
    dropzone.addEventListener('drop', (e) => {
        if (e.dataTransfer.files[0]) acceptFile(e.dataTransfer.files[0]);
    });

    fileClear.addEventListener('click', (e) => {
        e.stopPropagation();
        selectedFile = null;
        fileInput.value = '';
        fileChip.classList.remove('show');
        updateGenerateEnabled();
    });

    if (window.jQuery && jQuery.fn.select2) {
        jQuery('#ctAgentSelect').select2({ placeholder: 'Search for an agent…' });
        jQuery('#ctAgentSelect').on('change', updateGenerateEnabled);
    } else {
        agentSelect.addEventListener('change', updateGenerateEnabled);
    }

    function startStatusRotation() {
        let i = 0;
        statusText.textContent = statusMessages[0];
        statusInterval = setInterval(() => {
            i = Math.min(i + 1, statusMessages.length - 1);
            statusText.textContent = statusMessages[i];
        }, 1800);
    }

    function stopStatusRotation() {
        clearInterval(statusInterval);
    }

    function showStage(stage) {
        uploadStage.style.display = stage === 'upload' ? 'block' : 'none';
        processingStage.classList.toggle('show', stage === 'processing');
        resultStage.classList.toggle('show', stage === 'result');
    }

    function renderTurns(turns) {
        const container = document.getElementById('ctTurnsContainer');
        container.innerHTML = turns.map((turn, idx) => `
            <div class="ct-turn ${turn.speaker}" style="animation-delay:${idx * 60}ms">
                <div class="ct-avatar">${turn.speaker === 'agent' ? '<i class="mdi mdi-headset"></i>' : '<i class="mdi mdi-account"></i>'}</div>
                <div class="flex-grow-1">
                    <div class="ct-ts">[${turn.timestamp_label || '--:--:--'}]</div>
                    <div class="ct-speaker">${escapeHtml(turn.speaker_label)}:</div>
                    <div class="ct-text">${escapeHtml(turn.text)}</div>
                </div>
                <button type="button" class="btn btn-sm btn-icon ct-turn-copy" title="Copy this exchange" data-text="${escapeHtml('[' + (turn.timestamp_label || '--:--:--') + '] ' + turn.speaker_label + ': ' + turn.text)}">
                    <i class="mdi mdi-content-copy"></i>
                </button>
            </div>
        `).join('');
    }

    document.getElementById('ctTurnsContainer').addEventListener('click', function (e) {
        const btn = e.target.closest('.ct-turn-copy');
        if (!btn) return;
        navigator.clipboard.writeText(btn.dataset.text).then(() => {
            const icon = btn.querySelector('i');
            icon.className = 'mdi mdi-check';
            btn.classList.add('ct-copy-flash');
            setTimeout(() => {
                icon.className = 'mdi mdi-content-copy';
                btn.classList.remove('ct-copy-flash');
            }, 1200);
        });
    });

    function formatDuration(seconds) {
        if (seconds === null || seconds === undefined) return 'Unknown duration';
        const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
        const s = Math.floor(seconds % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    function showResult(data) {
        lastResult = data;
        document.getElementById('ctResultFilename').textContent = data.original_filename;
        document.getElementById('ctResultAgent').textContent = data.agent_name;
        document.getElementById('ctMetaDuration').textContent = formatDuration(data.duration_seconds);
        document.getElementById('ctMetaWords').textContent = data.word_count;
        document.getElementById('ctMetaExchanges').textContent = data.exchange_count;
        document.getElementById('ctMetaProcessingTime').textContent = data.processing_time_ms ? (data.processing_time_ms / 1000).toFixed(1) + 's' : '—';
        renderTurns(data.turns);

        document.getElementById('ctExportTxt').href = routes.exportUrl(data.uuid, 'txt');
        document.getElementById('ctExportPdf').href = routes.exportUrl(data.uuid, 'pdf');
        document.getElementById('ctExportDocx').href = routes.exportUrl(data.uuid, 'docx');

        showStage('result');
    }

    generateBtn.addEventListener('click', function () {
        if (!selectedFile || !agentSelect.value) return;

        showStage('processing');
        uploadProgressBar.style.width = '0%';
        startStatusRotation();

        const formData = new FormData();
        formData.append('audio', selectedFile);
        formData.append('agent_user_id', agentSelect.value);
        formData.append('request_uuid', generateUuid());

        const xhr = new XMLHttpRequest();
        xhr.open('POST', routes.store);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken());
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.addEventListener('progress', function (e) {
            if (e.lengthComputable) {
                uploadProgressBar.style.width = Math.round((e.loaded / e.total) * 100) + '%';
            }
        });

        xhr.onload = function () {
            stopStatusRotation();
            let res;
            try {
                res = JSON.parse(xhr.responseText);
            } catch (err) {
                res = { success: false, message: 'Unexpected server response.' };
            }

            if (xhr.status >= 200 && xhr.status < 300 && res.success) {
                showResult(res.data);
            } else {
                showStage('upload');
                showFileError(res.message || 'Something went wrong while generating the transcript.');
            }
        };

        xhr.onerror = function () {
            stopStatusRotation();
            showStage('upload');
            showFileError('Network error. Please check your connection and try again.');
        };

        xhr.send(formData);
    });

    document.getElementById('ctNewTranscriptBtn').addEventListener('click', function () {
        selectedFile = null;
        fileInput.value = '';
        fileChip.classList.remove('show');
        clearFileError();
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#ctAgentSelect').val('').trigger('change');
        } else {
            agentSelect.value = '';
        }
        updateGenerateEnabled();
        showStage('upload');
    });

    document.getElementById('ctCopyAllBtn').addEventListener('click', function () {
        if (!lastResult) return;
        const text = lastResult.turns.map(t => `[${t.timestamp_label || '--:--:--'}]\n${t.speaker_label}:\n${t.text}\n`).join('\n');
        navigator.clipboard.writeText(text).then(() => {
            const btn = this;
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="mdi mdi-check me-1"></i> Copied!';
            btn.classList.add('ct-copy-flash');
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('ct-copy-flash');
            }, 1500);
        });
    });
}());
</script>
@endsection
