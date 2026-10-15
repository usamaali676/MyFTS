# AI-Powered Call Transcription Module — Design

Date: 2026-07-25
Status: Approved by user, pending spec review

## Goal

Standalone CRM module: upload a call recording, transcribe it with OpenAI
`gpt-4o-mini-transcribe`, label turns as the selected CRM agent vs "Client",
show a polished animated transcript viewer, support copy + TXT/PDF/DOCX
export. Audio is never persisted anywhere. Zero modifications to existing
CRM functionality.

## Constraints discovered during exploration

- Stack is Laravel + Blade + vanilla JS + Bootstrap 5 (Sneat admin theme).
  No Vue/React — the spec's "use Framer Motion" recommendation does not
  apply; animations are CSS keyframes + a small JS state machine, matching
  the pattern already used in `resources/views/pages/ai-assistant/index.blade.php`.
- `openai-php/laravel` is already installed and configured (`OPENAI_API_KEY`
  slot exists in `.env.example`). `AIController` is the closest existing
  precedent for calling the OpenAI SDK and error-handling patterns.
- OpenAI's transcription API does not perform true speaker diarization —
  it returns one merged text stream (optionally with segment timestamps).
- `ffmpeg` is not installed on this dev machine (checked PATH and the
  Laragon install tree) — no path to audio chunking right now.
- No PDF/DOCX libraries are installed yet.
- `gpt-4o-mini-transcribe` does not support `verbose_json` / segment or
  word timestamps (that's whisper-1 only) — it returns plain text. Real
  per-word timestamps are not available from this model.
- Permission system (`PermissionMiddelware` + `GlobalHelper::Permissions()`)
  derives the permission list dynamically from route names matching
  `entity.operation` — registering routes named `calltranscription.*` makes
  a "Calltranscription" permission auto-appear in Roles & Permissions with
  no changes to `GlobalHelper`.
- `select2` is already bundled in the theme and used elsewhere (e.g.
  `pages/sale/create.blade.php`, `pages/attendance.blade.php`) — reuse it
  for the searchable agent dropdown instead of adding a new dependency.
- "Agent" = any CRM `User` (roles are wildly varied — Creator, TSR,
  Closer, QA, etc. — there is no single "Agent" role to filter on), picked
  via searchable dropdown, not restricted by role.

## Decisions (confirmed with user)

1. **Speaker attribution**: transcribe with segment timestamps, then a
   second cheap `gpt-4o-mini` chat completion call segments the transcript
   into `agent`/`client` turns using conversational cues (temperature 0,
   strict JSON output). This is heuristic, not true voice-based diarization
   — acceptable tradeoff to stay within the "OpenAI GPT-4o Mini only" /
   lowest-cost constraint.
2. **Long recordings**: no ffmpeg available, so no chunking in this MVP.
   Enforce a single-request size/duration cap that fits OpenAI's ~25MB
   request limit, with a clear rejection message above that. Chunked
   support is an explicit future follow-up once ffmpeg is provisioned.
3. **Processing model**: synchronous, single HTTP request per generate
   call (upload → transcribe → diarize → persist → respond). No queue
   worker dependency. All pipeline logic lives in a
   `CallTranscriptionService` class so it can be wrapped in a queued job
   later (for chunked/long-call support) without rewriting the core logic.
4. **Access control**: gated by a new `Calltranscription` Permission row
   (per-role, via the existing Roles & Permissions screen), same
   enforcement mechanism as `attendance`/`salereport`. Sidebar entry only
   renders for roles with `view = 1` on that permission.
5. **Export dependencies**: add `barryvdh/laravel-dompdf` (PDF) and
   `phpoffice/phpword` (DOCX) via composer — additive only.
6. **Timestamps**: since `gpt-4o-mini-transcribe` returns no real
   timestamps, add `james-heinrich/getid3` (pure-PHP, no ffmpeg binary
   required) to read the actual total audio duration from the file
   header. Per-turn `[00:00:02]` timestamps are then estimated by
   distributing that real duration proportionally across turns by word
   count — not frame-accurate, but no extra API calls and no ffmpeg
   dependency.
7. **Audio storage**: never write the uploaded audio to any disk/storage
   at all. Read directly from the PHP-managed upload tmp path and stream
   to OpenAI; let PHP's normal end-of-request cleanup remove it. This is
   strictly stronger than "delete after processing" since we never persist
   a copy in the first place.

## Data model

New table, no existing tables touched:

```
call_transcriptions
  id                     bigint PK
  uuid                   string, unique          -- public reference
  agent_user_id          FK -> users.id, nullable, on delete set null
  agent_name_snapshot     string                  -- captured at generation time
  original_filename       string
  duration_seconds        int, nullable
  file_size_bytes         int
  mime_type                string
  status                   enum: pending|processing|completed|failed
  transcript_json          json  -- [{speaker, text, start_seconds, end_seconds}, ...]
  word_count                int, nullable
  exchange_count            int, nullable
  processing_time_ms        int, nullable
  error_message              text, nullable
  openai_audio_tokens        int, nullable        -- cost tracking
  created_by                  FK -> users.id
  timestamps
  soft deletes
```

No audio blob column, ever.

## Backend

New files only:

- `database/migrations/xxxx_create_call_transcriptions_table.php`
- `app/Models/CallTranscription.php` — casts `transcript_json` to array.
- `app/Http/Requests/GenerateTranscriptRequest.php` — validates file
  (mimetype whitelist: mp3/wav/m4a/aac/ogg/webm; size cap) and
  `agent_user_id` (`exists:users,id`).
- `app/Services/CallTranscriptionService.php` — the pipeline:
  1. Validate file.
  2. Read `$file->getRealPath()` directly — never `store()`/`move()`.
  3. Read real total duration from the file via `getid3` (no ffmpeg).
  4. `OpenAI::audio()->transcribe()` with `gpt-4o-mini-transcribe`,
     `response_format: json` (plain text only — this model has no
     timestamp support).
  5. Second `OpenAI::chat()->create()` call (gpt-4o-mini, temperature 0,
     low max_tokens) to split the plain text into ordered agent/client
     turns given the agent's name.
  6. Map `agent` → selected user's real name, everything else → "Client".
     Estimate each turn's `[hh:mm:ss]` by distributing the real total
     duration proportionally across turns by word count.
  7. Compute word/exchange counts, processing time.
  8. Persist `CallTranscription` row.
  9. Retry-with-backoff (2 retries) on transient OpenAI errors; distinct
     catches for `RateLimitException` / `ServerException` /
     `TransporterException` / generic, following `AIController`'s
     existing error-handling pattern.
  9. Idempotency via a client-supplied request UUID — an existing
     processing/completed row for that UUID short-circuits reprocessing.
- `app/Services/TranscriptExportService.php` — `toTxt()`, `toPdf()`
  (dompdf view), `toDocx()` (PhpWord), all reading from the persisted
  `transcript_json`, no OpenAI calls.
- `app/Http/Controllers/CallTranscriptionController.php` — thin:
  `index`, `generate`, `show`, `export`, `destroy`. Delegates all logic to
  the two services.

New route group in `routes/web.php` (additive block only):

```php
Route::controller(CallTranscriptionController::class)
    ->prefix('call-transcription')->as('calltranscription.')
    ->middleware(PermissionMiddelware::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/generate', 'generate')->name('generate');
        Route::get('/{uuid}', 'show')->name('show');
        Route::get('/{uuid}/export/{format}', 'export')->name('export');
        Route::delete('/{uuid}', 'destroy')->name('destroy');
    });
```

## Frontend

`resources/views/pages/callTranscription/index.blade.php`,
`@extends('layouts.dashboard')`, same conventions as
`pages/ai-assistant/index.blade.php`. Vanilla JS + CSS keyframe animations
(no React/Framer Motion — not part of this stack). `select2` for the
agent dropdown. New gated `<li>` in `sidebar.blade.php`, same pattern as
the existing `attendance_perm`/`salereport_perm` checks.

Single-page, three-stage flow (panels swapped, no navigation):

1. **Upload** — drag-and-drop zone with hover/drag-over animation, file
   picker fallback, mobile tap-to-upload, client-side validation with
   shake-animation error state, searchable agent select2 dropdown,
   "Generate Transcript" button gated on both being set.
2. **Processing** — real upload progress bar (`XMLHttpRequest` progress
   events) during transfer, then animated CSS waveform bars with a
   rotating status message (client-side interval cycling "Uploading
   audio… → Processing recording… → Detecting speakers… → Identifying
   agent responses… → Generating transcript… → Finalizing transcript…")
   while the single generate request is in flight.
3. **Result** — fade/slide-in transcript, grouped speaker turn cards with
   stagger-in animation, collapsible per-speaker blocks, metadata strip
   (duration, word count, exchange count, processing time), copy-all /
   copy-selection with checkmark micro-animation, TXT/PDF/DOCX export
   dropdown, "New Transcript" reset.

All styling reuses existing Bootstrap/Sneat variables and classes
(`var(--bs-primary)`, `.btn`, `.card`, `.badge`) — no new design tokens.

## Security & privacy

- MIME-sniffed validation (not extension-only) + size cap, clear
  per-reason rejection messages.
- Audio never written to any disk by this app — no `storage/app` writes,
  nothing to clean up after the fact.
- Routes behind existing `auth` middleware + new `Calltranscription`
  permission.
- Errors logged via `Log::error` with no raw audio/PHI in log content,
  mirroring `AIController`'s logging pattern.

## Isolation guarantee

Only two touches to existing files: one new route group block appended
in `routes/web.php`, and one new gated `<li>` block appended in
`sidebar.blade.php`. No existing controller, model, migration, view, or
route is modified. Everything else is new files.

## Testing / verification

- Feature test: invalid file types/sizes rejected with correct messages.
- Feature test: full generate flow with the OpenAI client faked — correct
  persisted transcript, correct speaker labels, confirm no file written
  under `storage/app` during the request.
- Manual browser pass: drag-drop, agent search, processing animation,
  transcript render, copy, all three export formats, mobile viewport.
- Regression pass: `ai-assistant`, `lead`, `sale`, and the sidebar still
  behave unchanged.
