# Call Transcription Module Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a standalone, permission-gated Call Transcription module (upload audio → OpenAI `gpt-4o-mini-transcribe` → agent/client-labeled transcript → copy/export) with zero modification to any existing CRM behavior.

**Architecture:** New migration/model/service/controller/routes/view, all in new files. Two additive-only touches to existing files: one new route group block in `routes/web.php`, one new gated `<li>` in `sidebar.blade.php`. Processing is synchronous (one HTTP request per generate call), pipeline logic isolated in `CallTranscriptionService`. Audio is read directly from PHP's upload tmp path and streamed to OpenAI — never written to `storage/app` or any disk location this app controls.

**Tech Stack:** Laravel 11 (PHP 8.2+), Blade + vanilla JS + Bootstrap 5/Sneat theme (no Vue/React in this stack), `openai-php/laravel` (already installed), MySQL. New composer deps: `barryvdh/laravel-dompdf`, `phpoffice/phpword`, `james-heinrich/getid3`.

## Global Constraints

- Never modify existing controllers, models, migrations, views, or routes — only add new files, plus two additive edits (`routes/web.php` new block, `sidebar.blade.php` new `<li>`).
- Audio file is never written to any disk/storage location by this app — read directly from the PHP upload tmp path, stream to OpenAI, never `Storage::put`/`move()`.
- Transcription model: `gpt-4o-mini-transcribe`. It returns plain text only — no `verbose_json`, no segment/word timestamps (that's whisper-1 only). Confirmed by reading `vendor/openai-php/client/src/Resources/Audio.php` and the response DTO — the client is generic, but this model does not populate timestamp fields.
- Speaker split: a second `gpt-4o-mini` chat completion call (temperature 0, strict JSON) segments plain text into agent/client turns. Heuristic, not true voice diarization.
- Per-turn timestamps are estimated: real total audio duration read via `getid3` (pure PHP, no ffmpeg), distributed across turns proportionally by word count.
- No ffmpeg on this machine — no audio chunking. Single request must fit OpenAI's ~25MB cap; enforce a 24MB (24576 KB) server-side max.
- Allowed audio types: mp3, wav, m4a, aac, ogg, webm.
- Route names follow `entity.operation` so `App\Http\Middleware\PermissionMiddelware` (existing, unmodified) auto-gates them. Its regex only recognizes operations `index|view|create|store|edit|update|delete|conf-delete`, and its switch statement only *handles* `index|create|store|edit|update|conf-delete|delete` (an operation named literally `view` matches the regex but has no switch case, so it silently falls to `default => false` and is permanently denied — a pre-existing quirk, confirmed by reading `app/Http/Middleware/PermissionMiddelware.php`). Consequence: controller/route action names in this plan are `index`, `store`, `show`, `export`, `delete` — never `view` or `generate` or `destroy`, so permission gating lands where intended (`index`→view flag, `store`→create flag, `delete`→delete flag) and nothing gets silently locked out.
- Permission row name for this module: lowercase `calltranscription` (matches how `GlobalHelper::Permissions()` derives the checkbox key from the route name, and how the sidebar's existing `attendance`/`salereport` checks are written). No changes needed to `GlobalHelper.php` — it discovers permissions dynamically from route names.
- **Discovered, out of scope:** none of the existing dashboard routes in `routes/web.php` actually carry an `auth` middleware (checked — no `Route::middleware('auth')` group exists anywhere in the file, and no controller adds `$this->middleware('auth')`). This plan's new route group explicitly adds `auth` itself (good practice, spec requirement), but does **not** touch any existing route — that gap is pre-existing and out of scope here. Worth flagging to the user separately; not fixed by this plan.
- Isolated test database: local dev DB is `newcrm` (MySQL) — real working data. Tests must never run against it. This plan's first task provisions a separate `newcrm_testing` MySQL database and a `.env.testing` file so `RefreshDatabase`-based tests are fully isolated.
- `vendor/` is tracked in this repo (confirmed via `git ls-files vendor/` and the prior "vendor update" commit) — composer-driven changes must be committed together with `composer.json`/`composer.lock`.

---

### Task 1: Isolated test database + new composer dependencies

**Files:**
- Create: `.env.testing`
- Modify: `.gitignore` (append one line)
- Modify: `composer.json`, `composer.lock`, `vendor/**` (via composer require)

**Interfaces:**
- Produces: a working `--env=testing` Laravel environment against MySQL database `newcrm_testing`; `\getID3`, `Barryvdh\DomPDF\Facade\Pdf`, `PhpOffice\PhpWord\PhpWord`/`IOFactory` available to later tasks.

- [ ] **Step 1: Require the three new composer packages**

Run:
```bash
composer require barryvdh/laravel-dompdf phpoffice/phpword james-heinrich/getid3
```
Expected: completes with "Generating optimized autoload files" and no errors. This updates `composer.json`, `composer.lock`, and `vendor/`.

- [ ] **Step 2: Create the isolated test database**

Run:
```bash
php -r "$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', ''); $pdo->exec('CREATE DATABASE IF NOT EXISTS newcrm_testing');"
```
Expected: no output, exit code 0.

- [ ] **Step 3: Create `.env.testing`**

Read `.env`'s `APP_KEY` value first (`grep ^APP_KEY .env`) and reuse the same value below (Laravel requires a valid key to boot).

```env
APP_ENV=testing
APP_KEY=base64:H8SIYCJelKoLdSNZauN+v4K1xSyFPEDd171/HJfmA/E=
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=newcrm_testing
DB_USERNAME=root
DB_PASSWORD=
OPENAI_API_KEY=test-key-not-used-fake-only
QUEUE_CONNECTION=sync
MAIL_MAILER=array
SESSION_DRIVER=array
CACHE_STORE=array
```

(If `APP_KEY` in the real `.env` differs from the value shown above, use the actual value from `.env` instead — do not invent one.)

- [ ] **Step 4: Keep `.env.testing` out of git**

Append to `.gitignore` (it currently only ignores the exact names `.env`, `.env.backup`, `.env.production` — `.env.testing` is not covered):

```
.env.testing
```

- [ ] **Step 5: Verify the isolated environment migrates cleanly**

Run:
```bash
php artisan migrate --env=testing
```
Expected: runs every existing migration against `newcrm_testing` and ends with `INFO  Nothing to migrate.` on a second run, or a full list of "DONE" lines on the first. No errors. This proves `.env.testing` is wired correctly and the real `newcrm` database was never touched.

- [ ] **Step 6: Commit**

```bash
git add composer.json composer.lock vendor .gitignore
git commit -m "chore: add dompdf, phpword, getid3 deps and isolated test DB config"
```
(`.env.testing` is intentionally not staged — it's gitignored.)

---

### Task 2: `call_transcriptions` migration, model, and failure exception

**Files:**
- Create: `database/migrations/2026_07_25_120000_create_call_transcriptions_table.php`
- Create: `app/Models/CallTranscription.php`
- Create: `app/Exceptions/TranscriptionFailedException.php`
- Test: `tests/Feature/CallTranscriptionModelTest.php`

**Interfaces:**
- Produces:
  - `CallTranscription` Eloquent model, table `call_transcriptions`, fillable: `uuid, agent_user_id, agent_name_snapshot, original_filename, duration_seconds, file_size_bytes, mime_type, status, transcript_json, word_count, exchange_count, processing_time_ms, error_message, openai_audio_tokens, created_by`. `transcript_json` cast to `array`. `status` values: `pending|processing|completed|failed`.
  - `TranscriptionFailedException extends \RuntimeException` with public readonly `int $httpStatus`.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/CallTranscriptionModelTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\CallTranscription;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallTranscriptionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_transcript_json_as_array(): void
    {
        $role = Role::create(['name' => 'Test Role']);
        $agent = User::create([
            'name' => 'John Smith',
            'email' => 'john@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);

        $record = CallTranscription::create([
            'uuid' => 'test-uuid-1',
            'agent_user_id' => $agent->id,
            'agent_name_snapshot' => $agent->name,
            'original_filename' => 'call.mp3',
            'file_size_bytes' => 1024,
            'mime_type' => 'audio/mpeg',
            'status' => 'completed',
            'transcript_json' => [
                ['speaker' => 'agent', 'speaker_label' => 'John Smith', 'text' => 'Hello.', 'timestamp_seconds' => 0, 'timestamp_label' => '00:00:00'],
            ],
            'word_count' => 1,
            'exchange_count' => 1,
            'created_by' => $agent->id,
        ]);

        $fresh = CallTranscription::find($record->id);

        $this->assertIsArray($fresh->transcript_json);
        $this->assertSame('John Smith', $fresh->transcript_json[0]['speaker_label']);
        $this->assertSame('completed', $fresh->status);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CallTranscriptionModelTest`
Expected: FAIL — `call_transcriptions` table / `CallTranscription` class don't exist yet.

- [ ] **Step 3: Write the migration**

Create `database/migrations/2026_07_25_120000_create_call_transcriptions_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_transcriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('agent_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('agent_name_snapshot');
            $table->string('original_filename');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedBigInteger('file_size_bytes');
            $table->string('mime_type');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('transcript_json')->nullable();
            $table->unsignedInteger('word_count')->nullable();
            $table->unsignedInteger('exchange_count')->nullable();
            $table->unsignedInteger('processing_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('openai_audio_tokens')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_transcriptions');
    }
};
```

- [ ] **Step 4: Write the model**

Create `app/Models/CallTranscription.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallTranscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'agent_user_id',
        'agent_name_snapshot',
        'original_filename',
        'duration_seconds',
        'file_size_bytes',
        'mime_type',
        'status',
        'transcript_json',
        'word_count',
        'exchange_count',
        'processing_time_ms',
        'error_message',
        'openai_audio_tokens',
        'created_by',
    ];

    protected $casts = [
        'transcript_json' => 'array',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

- [ ] **Step 5: Write the exception**

Create `app/Exceptions/TranscriptionFailedException.php`:

```php
<?php

namespace App\Exceptions;

class TranscriptionFailedException extends \RuntimeException
{
    public function __construct(string $message, public readonly int $httpStatus = 500)
    {
        parent::__construct($message);
    }
}
```

- [ ] **Step 6: Run the migration in the isolated test DB and re-run the test**

Run:
```bash
php artisan migrate --env=testing
php artisan test --filter=CallTranscriptionModelTest
```
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_07_25_120000_create_call_transcriptions_table.php app/Models/CallTranscription.php app/Exceptions/TranscriptionFailedException.php tests/Feature/CallTranscriptionModelTest.php
git commit -m "feat: add call_transcriptions table, model, and failure exception"
```

---

### Task 3: `CallTranscriptionService` — transcribe, diarize, estimate timestamps

**Files:**
- Create: `app/Services/CallTranscriptionService.php`
- Create: `tests/Concerns/MakesSilentWav.php`
- Test: `tests/Feature/CallTranscriptionServiceTest.php`

**Interfaces:**
- Consumes: `CallTranscription` model (Task 2), `TranscriptionFailedException` (Task 2), `OpenAI` facade (`OpenAI\Laravel\Facades\OpenAI`, already installed).
- Produces: `CallTranscriptionService::generate(\Illuminate\Http\UploadedFile $file, \App\Models\User $agent, string $requestUuid, int $createdByUserId): \App\Models\CallTranscription`. Throws `TranscriptionFailedException` on unrecoverable failure (record is marked `failed` first). Idempotent on `$requestUuid`: a second call with the same UUID while a `processing`/`completed` row exists returns that row without calling OpenAI again.
- Transcript turn shape stored in `transcript_json`: `['speaker' => 'agent'|'client', 'speaker_label' => string, 'text' => string, 'timestamp_seconds' => ?int, 'timestamp_label' => ?string]`.

- [ ] **Step 1: Add the shared WAV-fixture test helper**

Create `tests/Concerns/MakesSilentWav.php`:

```php
<?php

namespace Tests\Concerns;

use Illuminate\Http\UploadedFile;

trait MakesSilentWav
{
    protected function makeSilentWavUploadedFile(int $seconds, string $originalName = 'call.wav'): UploadedFile
    {
        $sampleRate = 8000;
        $bitsPerSample = 8;
        $channels = 1;
        $numSamples = $sampleRate * $seconds;
        $dataSize = $numSamples * $channels * intdiv($bitsPerSample, 8);

        $header = 'RIFF'
            . pack('V', 36 + $dataSize)
            . 'WAVE'
            . 'fmt '
            . pack('V', 16)
            . pack('v', 1)
            . pack('v', $channels)
            . pack('V', $sampleRate)
            . pack('V', $sampleRate * $channels * intdiv($bitsPerSample, 8))
            . pack('v', $channels * intdiv($bitsPerSample, 8))
            . pack('v', $bitsPerSample)
            . 'data'
            . pack('V', $dataSize);

        $data = str_repeat(chr(128), $dataSize);

        $path = tempnam(sys_get_temp_dir(), 'wav') . '.wav';
        file_put_contents($path, $header . $data);

        return new UploadedFile($path, $originalName, 'audio/wav', null, true);
    }
}
```

- [ ] **Step 2: Write the failing tests**

Create `tests/Feature/CallTranscriptionServiceTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Exceptions\TranscriptionFailedException;
use App\Models\CallTranscription;
use App\Models\Role;
use App\Models\User;
use App\Services\CallTranscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\Concerns\MakesSilentWav;
use Tests\TestCase;

class CallTranscriptionServiceTest extends TestCase
{
    use RefreshDatabase;
    use MakesSilentWav;

    private function makeAgent(): User
    {
        $role = Role::create(['name' => 'Test Role']);

        return User::create([
            'name' => 'John Smith',
            'email' => 'john+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_it_transcribes_diarizes_and_estimates_timestamps(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake([
                'text' => 'Hello thank you for calling. Hi I need help with my order. I would be happy to assist.',
            ]),
            CreateResponse::fake([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'turns' => [
                                ['speaker' => 'agent', 'text' => 'Hello thank you for calling.'],
                                ['speaker' => 'client', 'text' => 'Hi I need help with my order.'],
                                ['speaker' => 'agent', 'text' => 'I would be happy to assist.'],
                            ],
                        ]),
                    ],
                ]],
            ]),
        ]);

        $file = $this->makeSilentWavUploadedFile(4);

        $record = app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-1', $agent->id);

        $this->assertSame('completed', $record->status);
        $this->assertSame(4, $record->duration_seconds);
        $this->assertSame(3, $record->exchange_count);
        $this->assertSame(18, $record->word_count);

        $turns = $record->transcript_json;
        $this->assertSame('John Smith', $turns[0]['speaker_label']);
        $this->assertSame('Client', $turns[1]['speaker_label']);
        $this->assertSame('John Smith', $turns[2]['speaker_label']);

        $this->assertSame(0, $turns[0]['timestamp_seconds']);
        $this->assertSame(1, $turns[1]['timestamp_seconds']);
        $this->assertSame(3, $turns[2]['timestamp_seconds']);
        $this->assertSame('00:00:03', $turns[2]['timestamp_label']);
    }

    public function test_it_is_idempotent_on_request_uuid(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake(['text' => 'Hello.']),
            CreateResponse::fake([
                'choices' => [[
                    'message' => ['content' => json_encode(['turns' => [['speaker' => 'agent', 'text' => 'Hello.']]])],
                ]],
            ]),
        ]);

        $service = app(CallTranscriptionService::class);
        $file = $this->makeSilentWavUploadedFile(1);

        $first = $service->generate($file, $agent, 'dup-uuid', $agent->id);
        $second = $service->generate($this->makeSilentWavUploadedFile(1), $agent, 'dup-uuid', $agent->id);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, CallTranscription::count());
    }

    public function test_it_fails_when_transcription_returns_no_speech(): void
    {
        $agent = $this->makeAgent();

        OpenAI::fake([
            TranscriptionResponse::fake(['text' => '']),
        ]);

        $file = $this->makeSilentWavUploadedFile(1);

        $this->expectException(TranscriptionFailedException::class);

        try {
            app(CallTranscriptionService::class)->generate($file, $agent, 'req-uuid-empty', $agent->id);
        } finally {
            $this->assertSame('failed', CallTranscription::where('uuid', 'req-uuid-empty')->first()->status);
        }
    }
}
```

- [ ] **Step 3: Run tests to verify they fail**

Run: `php artisan test --filter=CallTranscriptionServiceTest`
Expected: FAIL — `CallTranscriptionService` doesn't exist yet.

- [ ] **Step 4: Write the service**

Create `app/Services/CallTranscriptionService.php`:

```php
<?php

namespace App\Services;

use App\Exceptions\TranscriptionFailedException;
use App\Models\CallTranscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class CallTranscriptionService
{
    private const TRANSCRIBE_MODEL = 'gpt-4o-mini-transcribe';
    private const DIARIZE_MODEL = 'gpt-4o-mini';
    private const MAX_RETRIES = 2;

    public function generate(UploadedFile $file, User $agent, string $requestUuid, int $createdByUserId): CallTranscription
    {
        $existing = CallTranscription::where('uuid', $requestUuid)
            ->whereIn('status', ['processing', 'completed'])
            ->first();

        if ($existing) {
            return $existing;
        }

        $record = CallTranscription::create([
            'uuid' => $requestUuid,
            'agent_user_id' => $agent->id,
            'agent_name_snapshot' => $agent->name,
            'original_filename' => $file->getClientOriginalName(),
            'file_size_bytes' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'processing',
            'created_by' => $createdByUserId,
        ]);

        $startedAt = microtime(true);

        try {
            $durationSeconds = $this->readDuration($file->getRealPath());
            $text = $this->transcribe($file);
            $rawTurns = $this->diarize($text, $agent->name);
            $turns = $this->finalizeTurns($rawTurns, $agent->name, $durationSeconds);

            $record->update([
                'status' => 'completed',
                'transcript_json' => $turns,
                'duration_seconds' => $durationSeconds,
                'word_count' => $this->countWords($turns),
                'exchange_count' => count($turns),
                'processing_time_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (TranscriptionFailedException $e) {
            $record->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Call transcription failed', [
                'call_transcription_id' => $record->id,
                'message' => $e->getMessage(),
            ]);
            $record->update(['status' => 'failed', 'error_message' => 'An unexpected error occurred.']);
            throw new TranscriptionFailedException('An unexpected error occurred while processing the recording.', 500);
        }

        return $record->fresh();
    }

    private function readDuration(string $path): ?int
    {
        try {
            $getID3 = new \getID3();
            $info = $getID3->analyze($path);
            $seconds = $info['playtime_seconds'] ?? null;

            return $seconds !== null ? (int) round($seconds) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function transcribe(UploadedFile $file): string
    {
        $attempt = 0;

        while (true) {
            try {
                $response = OpenAI::audio()->transcribe([
                    'model' => self::TRANSCRIBE_MODEL,
                    'file' => fopen($file->getRealPath(), 'r'),
                    'response_format' => 'json',
                ]);

                $text = trim($response->text);

                if ($text === '') {
                    throw new TranscriptionFailedException('No speech was detected in the uploaded recording.', 422);
                }

                return $text;
            } catch (TranscriptionFailedException $e) {
                throw $e;
            } catch (\OpenAI\Exceptions\RateLimitException|\OpenAI\Exceptions\ServerException|\OpenAI\Exceptions\TransporterException $e) {
                if (++$attempt > self::MAX_RETRIES) {
                    throw new TranscriptionFailedException('The transcription service is temporarily unavailable. Please try again shortly.', 503);
                }
                usleep(300000 * $attempt);
            } catch (\OpenAI\Exceptions\ErrorException $e) {
                throw new TranscriptionFailedException('The transcription service rejected the audio file: ' . $e->getMessage(), 422);
            }
        }
    }

    /**
     * @return array<int, array{speaker: string, text: string}>
     */
    private function diarize(string $text, string $agentName): array
    {
        $prompt = <<<PROMPT
            You are given a raw phone-call transcript with no speaker labels, between two people:
            - "{$agentName}" (the agent / call center representative)
            - the caller (their customer)

            Split the transcript into an ordered list of speaking turns. For each turn, decide whether
            it was spoken by the agent or the client based on conversational role (who is greeting and
            assisting versus who is requesting help or answering questions about their own account).

            Return strict JSON only, in this exact shape, no prose:
            {"turns": [{"speaker": "agent", "text": "..."}, {"speaker": "client", "text": "..."}]}

            Transcript:
            {$text}
            PROMPT;

        $attempt = 0;

        while (true) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => self::DIARIZE_MODEL,
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => 'You split call transcripts into labeled speaker turns and respond with strict JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                $decoded = json_decode($response->choices[0]->message->content, true);
                $turns = $decoded['turns'] ?? null;

                if (!is_array($turns) || count($turns) === 0) {
                    throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
                }

                $mapped = array_values(array_filter(array_map(static function (array $t): array {
                    return [
                        'speaker' => ($t['speaker'] ?? '') === 'agent' ? 'agent' : 'client',
                        'text' => trim((string) ($t['text'] ?? '')),
                    ];
                }, $turns), static fn (array $t): bool => $t['text'] !== ''));

                if (count($mapped) === 0) {
                    throw new TranscriptionFailedException('Could not identify speaker turns in the recording.', 422);
                }

                return $mapped;
            } catch (TranscriptionFailedException $e) {
                throw $e;
            } catch (\OpenAI\Exceptions\RateLimitException|\OpenAI\Exceptions\ServerException|\OpenAI\Exceptions\TransporterException $e) {
                if (++$attempt > self::MAX_RETRIES) {
                    throw new TranscriptionFailedException('The transcription service is temporarily unavailable. Please try again shortly.', 503);
                }
                usleep(300000 * $attempt);
            } catch (\OpenAI\Exceptions\ErrorException $e) {
                throw new TranscriptionFailedException('The transcription service encountered an error while analyzing speakers.', 422);
            }
        }
    }

    /**
     * @param  array<int, array{speaker: string, text: string}>  $turns
     * @return array<int, array{speaker: string, speaker_label: string, text: string, timestamp_seconds: ?int, timestamp_label: ?string}>
     */
    private function finalizeTurns(array $turns, string $agentName, ?int $durationSeconds): array
    {
        $totalWords = max(1, array_sum(array_map(static fn (array $t): int => str_word_count($t['text']), $turns)));
        $elapsedWords = 0;
        $result = [];

        foreach ($turns as $turn) {
            $words = str_word_count($turn['text']);
            $startSeconds = $durationSeconds !== null
                ? (int) round(($elapsedWords / $totalWords) * $durationSeconds)
                : null;
            $elapsedWords += $words;

            $result[] = [
                'speaker' => $turn['speaker'],
                'speaker_label' => $turn['speaker'] === 'agent' ? $agentName : 'Client',
                'text' => $turn['text'],
                'timestamp_seconds' => $startSeconds,
                'timestamp_label' => $startSeconds !== null ? $this->formatTimestamp($startSeconds) : null,
            ];
        }

        return $result;
    }

    private function formatTimestamp(int $seconds): string
    {
        return sprintf('%02d:%02d:%02d', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
    }

    /**
     * @param  array<int, array{text: string}>  $turns
     */
    private function countWords(array $turns): int
    {
        return array_sum(array_map(static fn (array $t): int => str_word_count($t['text']), $turns));
    }
}
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=CallTranscriptionServiceTest`
Expected: PASS (all 3 tests).

- [ ] **Step 6: Commit**

```bash
git add app/Services/CallTranscriptionService.php tests/Concerns/MakesSilentWav.php tests/Feature/CallTranscriptionServiceTest.php
git commit -m "feat: add CallTranscriptionService (transcribe, diarize, estimate timestamps)"
```

---

### Task 4: Validation, controller, routes, and permission gating

**Files:**
- Create: `app/Http/Requests/GenerateTranscriptRequest.php`
- Create: `app/Http/Controllers/CallTranscriptionController.php`
- Modify: `routes/web.php` (append new block only — nothing else touched)
- Test: `tests/Feature/CallTranscriptionControllerTest.php`

**Interfaces:**
- Consumes: `CallTranscriptionService::generate()` (Task 3), `TranscriptionFailedException` (Task 2), `MakesSilentWav` trait (Task 3).
- Produces: routes `calltranscription.index` (GET `/call-transcription`), `calltranscription.store` (POST `/call-transcription`), `calltranscription.show` (GET `/call-transcription/{uuid}`), `calltranscription.delete` (DELETE `/call-transcription/{uuid}`). `store` returns JSON `{success, data: {uuid, status, agent_name, original_filename, duration_seconds, word_count, exchange_count, processing_time_ms, turns}}` on success, `{success: false, message}` with the exception's `httpStatus` on failure.

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/CallTranscriptionControllerTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\Concerns\MakesSilentWav;
use Tests\TestCase;

class CallTranscriptionControllerTest extends TestCase
{
    use RefreshDatabase;
    use MakesSilentWav;

    private function makeUserWithPermission(bool $canView, bool $canCreate): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'calltranscription',
            'create' => $canCreate ? 1 : 0,
            'view' => $canView ? 1 : 0,
            'edit' => 0,
            'delete' => 1,
        ]);

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('calltranscription.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_denied_without_view_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: false, canCreate: false);

        $response = $this->actingAs($user)->get(route('calltranscription.index'));

        $response->assertRedirect(route('home'));
    }

    public function test_index_allowed_with_view_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->get(route('calltranscription.index'));

        $response->assertOk();
    }

    public function test_store_generates_transcript_for_authorized_user(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $agent = $this->makeUserWithPermission(canView: true, canCreate: true);

        OpenAI::fake([
            TranscriptionResponse::fake(['text' => 'Hello there.']),
            CreateResponse::fake([
                'choices' => [[
                    'message' => ['content' => json_encode(['turns' => [['speaker' => 'agent', 'text' => 'Hello there.']]])],
                ]],
            ]),
        ]);

        $response = $this->actingAs($user)->post(route('calltranscription.store'), [
            'audio' => $this->makeSilentWavUploadedFile(2),
            'agent_user_id' => $agent->id,
            'request_uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.agent_name', $agent->name);
        $response->assertJsonPath('data.turns.0.speaker_label', $agent->name);
    }

    public function test_store_denied_without_create_permission(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: false);
        $agent = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->post(route('calltranscription.store'), [
            'audio' => $this->makeSilentWavUploadedFile(1),
            'agent_user_id' => $agent->id,
            'request_uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response->assertRedirect(route('home'));
    }

    public function test_store_rejects_unsupported_file_type(): void
    {
        $user = $this->makeUserWithPermission(canView: true, canCreate: true);
        $agent = $this->makeUserWithPermission(canView: true, canCreate: true);

        $response = $this->actingAs($user)->post(route('calltranscription.store'), [
            'audio' => \Illuminate\Http\UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
            'agent_user_id' => $agent->id,
            'request_uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response->assertSessionHasErrors('audio');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=CallTranscriptionControllerTest`
Expected: FAIL — route `calltranscription.index` not defined.

- [ ] **Step 3: Write the form request**

Create `app/Http/Requests/GenerateTranscriptRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTranscriptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'audio' => ['required', 'file', 'mimes:mp3,wav,m4a,aac,ogg,webm', 'max:24576'],
            'agent_user_id' => ['required', 'integer', 'exists:users,id'],
            'request_uuid' => ['required', 'uuid'],
        ];
    }

    public function messages(): array
    {
        return [
            'audio.required' => 'Please choose an audio file to upload.',
            'audio.mimes' => 'Unsupported file type. Supported formats: MP3, WAV, M4A, AAC, OGG, WEBM.',
            'audio.max' => 'File is too large. Maximum size is 24 MB per recording.',
            'agent_user_id.required' => 'Please select the agent for this call.',
            'agent_user_id.exists' => 'Selected agent could not be found.',
        ];
    }
}
```

- [ ] **Step 4: Write the controller**

Create `app/Http/Controllers/CallTranscriptionController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Exceptions\TranscriptionFailedException;
use App\Http\Requests\GenerateTranscriptRequest;
use App\Models\CallTranscription;
use App\Models\User;
use App\Services\CallTranscriptionService;
use Illuminate\Support\Facades\Auth;

class CallTranscriptionController extends Controller
{
    public function __construct(
        private readonly CallTranscriptionService $service,
    ) {}

    public function index()
    {
        $agents = User::orderBy('name')->get(['id', 'name']);

        return view('pages.callTranscription.index', compact('agents'));
    }

    public function store(GenerateTranscriptRequest $request)
    {
        $agent = User::findOrFail($request->integer('agent_user_id'));

        try {
            $record = $this->service->generate(
                $request->file('audio'),
                $agent,
                (string) $request->string('request_uuid'),
                Auth::id(),
            );
        } catch (TranscriptionFailedException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->httpStatus);
        }

        return response()->json(['success' => true, 'data' => $this->transformRecord($record)]);
    }

    public function show(string $uuid)
    {
        $record = CallTranscription::where('uuid', $uuid)->firstOrFail();

        return response()->json(['success' => true, 'data' => $this->transformRecord($record)]);
    }

    public function delete(string $uuid)
    {
        $record = CallTranscription::where('uuid', $uuid)->firstOrFail();
        $record->delete();

        return response()->json(['success' => true]);
    }

    private function transformRecord(CallTranscription $record): array
    {
        return [
            'uuid' => $record->uuid,
            'status' => $record->status,
            'agent_name' => $record->agent_name_snapshot,
            'original_filename' => $record->original_filename,
            'duration_seconds' => $record->duration_seconds,
            'word_count' => $record->word_count,
            'exchange_count' => $record->exchange_count,
            'processing_time_ms' => $record->processing_time_ms,
            'turns' => $record->transcript_json ?? [],
        ];
    }
}
```

- [ ] **Step 5: Append the route group**

In `routes/web.php`, add the import near the other `use App\Http\Controllers\...;` lines:

```php
use App\Http\Controllers\CallTranscriptionController;
```

Then append this new block at the end of the file (after the existing last route definition — do not reorder or touch anything above it):

```php
Route::controller(CallTranscriptionController::class)
    ->prefix('call-transcription')
    ->as('calltranscription.')
    ->middleware(['auth', PermissionMiddelware::class])
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{uuid}', 'show')->name('show');
        Route::delete('/{uuid}', 'delete')->name('delete');
    });
```

(The `export` route is added in Task 5 alongside `TranscriptExportService`.)

- [ ] **Step 6: Create the minimal placeholder view so `index` doesn't 500**

Create `resources/views/pages/callTranscription/index.blade.php` (full UI comes in Task 7 — this is a minimal valid placeholder so Task 4's tests pass now):

```blade
@extends('layouts.dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4>Call Transcription</h4>
    <p>Coming soon.</p>
</div>
@endsection
```

- [ ] **Step 7: Run tests to verify they pass**

Run: `php artisan test --filter=CallTranscriptionControllerTest`
Expected: PASS (all 6 tests).

- [ ] **Step 8: Commit**

```bash
git add app/Http/Requests/GenerateTranscriptRequest.php app/Http/Controllers/CallTranscriptionController.php routes/web.php resources/views/pages/callTranscription/index.blade.php tests/Feature/CallTranscriptionControllerTest.php
git commit -m "feat: add call transcription routes, controller, and permission-gated access"
```

---

### Task 5: Export (TXT/PDF/DOCX)

**Files:**
- Create: `app/Services/TranscriptExportService.php`
- Create: `resources/views/pages/callTranscription/exports/pdf.blade.php`
- Modify: `app/Http/Controllers/CallTranscriptionController.php` (add `export` method + inject the new service)
- Modify: `routes/web.php` (append one route line)
- Test: `tests/Feature/CallTranscriptionExportTest.php`

**Interfaces:**
- Consumes: `CallTranscription` model (Task 2).
- Produces: `TranscriptExportService::toTxt(CallTranscription $record)`, `::toPdf(CallTranscription $record)`, `::toDocx(CallTranscription $record)` — each returns a downloadable HTTP response. Route `calltranscription.export` (GET `/call-transcription/{uuid}/export/{format}`).

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/CallTranscriptionExportTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\CallTranscription;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallTranscriptionExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeAuthorizedUser(): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        Permission::create([
            'role_id' => $role->id,
            'name' => 'calltranscription',
            'create' => 1,
            'view' => 1,
            'edit' => 0,
            'delete' => 1,
        ]);

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    private function makeRecord(User $user): CallTranscription
    {
        return CallTranscription::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'agent_user_id' => $user->id,
            'agent_name_snapshot' => $user->name,
            'original_filename' => 'call.wav',
            'file_size_bytes' => 2048,
            'mime_type' => 'audio/wav',
            'status' => 'completed',
            'transcript_json' => [
                ['speaker' => 'agent', 'speaker_label' => $user->name, 'text' => 'Hello there.', 'timestamp_seconds' => 0, 'timestamp_label' => '00:00:00'],
                ['speaker' => 'client', 'speaker_label' => 'Client', 'text' => 'Hi, I need help.', 'timestamp_seconds' => 2, 'timestamp_label' => '00:00:02'],
            ],
            'word_count' => 7,
            'exchange_count' => 2,
            'created_by' => $user->id,
        ]);
    }

    public function test_txt_export_downloads_with_expected_content(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'txt']));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('Hello there.', $response->getContent());
        $this->assertStringContainsString('Client:', $response->getContent());
    }

    public function test_pdf_export_downloads(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'pdf']));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_docx_export_downloads(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'docx']));

        $response->assertOk();
        $this->assertStringContainsString('wordprocessingml', $response->headers->get('Content-Type'));
    }

    public function test_unknown_format_returns_404(): void
    {
        $user = $this->makeAuthorizedUser();
        $record = $this->makeRecord($user);

        $response = $this->actingAs($user)->get(route('calltranscription.export', ['uuid' => $record->uuid, 'format' => 'exe']));

        $response->assertNotFound();
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CallTranscriptionExportTest`
Expected: FAIL — route `calltranscription.export` not defined.

- [ ] **Step 3: Write the export service**

Create `app/Services/TranscriptExportService.php`:

```php
<?php

namespace App\Services;

use App\Models\CallTranscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TranscriptExportService
{
    public function toTxt(CallTranscription $record): Response
    {
        $lines = [];

        foreach ($record->transcript_json ?? [] as $turn) {
            $lines[] = '[' . ($turn['timestamp_label'] ?? '--:--:--') . ']';
            $lines[] = $turn['speaker_label'] . ':';
            $lines[] = $turn['text'];
            $lines[] = '';
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transcript-' . $record->uuid . '.txt"',
        ]);
    }

    public function toPdf(CallTranscription $record)
    {
        return Pdf::loadView('pages.callTranscription.exports.pdf', ['record' => $record])
            ->download('transcript-' . $record->uuid . '.pdf');
    }

    public function toDocx(CallTranscription $record): BinaryFileResponse
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Call Transcript', ['bold' => true, 'size' => 16]);
        $section->addText('Agent: ' . $record->agent_name_snapshot);
        $section->addTextBreak(1);

        foreach ($record->transcript_json ?? [] as $turn) {
            $section->addText('[' . ($turn['timestamp_label'] ?? '--:--:--') . '] ' . $turn['speaker_label'] . ':', ['bold' => true]);
            $section->addText($turn['text']);
            $section->addTextBreak(1);
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'docx');
        IOFactory::createWriter($phpWord, 'Word2007')->save($tempPath);

        return response()->download($tempPath, 'transcript-' . $record->uuid . '.docx')->deleteFileAfterSend(true);
    }
}
```

- [ ] **Step 4: Write the PDF template**

Create `resources/views/pages/callTranscription/exports/pdf.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #2e263d; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 16px; }
        .turn { margin-bottom: 12px; }
        .turn .ts { color: #a1acb8; font-size: 10px; }
        .turn .speaker { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Call Transcript</h1>
    <div class="meta">
        Agent: {{ $record->agent_name_snapshot }} &middot;
        Duration: {{ $record->duration_seconds !== null ? gmdate('H:i:s', $record->duration_seconds) : 'Unknown' }} &middot;
        Words: {{ $record->word_count }} &middot;
        Exchanges: {{ $record->exchange_count }}
    </div>

    @foreach ($record->transcript_json ?? [] as $turn)
        <div class="turn">
            <div class="ts">[{{ $turn['timestamp_label'] ?? '--:--:--' }}]</div>
            <div class="speaker">{{ $turn['speaker_label'] }}:</div>
            <div>{{ $turn['text'] }}</div>
        </div>
    @endforeach
</body>
</html>
```

- [ ] **Step 5: Wire the controller and route**

In `app/Http/Controllers/CallTranscriptionController.php`, add the import and constructor parameter:

```php
use App\Services\TranscriptExportService;
```

Change the constructor to:

```php
public function __construct(
    private readonly CallTranscriptionService $service,
    private readonly TranscriptExportService $exportService,
) {}
```

Add this method to the class:

```php
public function export(string $uuid, string $format)
{
    $record = CallTranscription::where('uuid', $uuid)->firstOrFail();

    return match ($format) {
        'txt' => $this->exportService->toTxt($record),
        'pdf' => $this->exportService->toPdf($record),
        'docx' => $this->exportService->toDocx($record),
        default => abort(404),
    };
}
```

In `routes/web.php`, add one line inside the existing `calltranscription.` group (from Task 4), right after the `show` route:

```php
Route::get('/{uuid}/export/{format}', 'export')->name('export');
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --filter=CallTranscriptionExportTest`
Expected: PASS (all 4 tests).

- [ ] **Step 7: Run the full test suite so far**

Run: `php artisan test`
Expected: all tests across all files PASS (Task 2, 3, 4, 5 tests + the pre-existing `ExampleTest`).

- [ ] **Step 8: Commit**

```bash
git add app/Services/TranscriptExportService.php resources/views/pages/callTranscription/exports/pdf.blade.php app/Http/Controllers/CallTranscriptionController.php routes/web.php tests/Feature/CallTranscriptionExportTest.php
git commit -m "feat: add TXT/PDF/DOCX transcript export"
```

---

### Task 6: Sidebar navigation entry (permission-gated)

**Files:**
- Modify: `resources/views/layouts/partials/sidebar.blade.php` (append one gated `<li>` — nothing else in the file changes)
- Test: `tests/Feature/CallTranscriptionSidebarTest.php`

**Interfaces:**
- Consumes: existing `$user`/`Permission` pattern already in `sidebar.blade.php` (same as `$attendance_perm`).

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/CallTranscriptionSidebarTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CallTranscriptionSidebarTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(?bool $canView): User
    {
        $role = Role::create(['name' => 'Test Role ' . uniqid()]);

        if ($canView !== null) {
            Permission::create([
                'role_id' => $role->id,
                'name' => 'calltranscription',
                'create' => 0,
                'view' => $canView ? 1 : 0,
                'edit' => 0,
                'delete' => 0,
            ]);
        }

        return User::create([
            'name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.test',
            'password' => bcrypt('secret'),
            'role_id' => $role->id,
        ]);
    }

    public function test_sidebar_shows_link_when_permission_granted(): void
    {
        $user = $this->makeUser(canView: true);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('calltranscription.index'), false);
    }

    public function test_sidebar_hides_link_when_permission_missing(): void
    {
        $user = $this->makeUser(canView: null);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertDontSee(route('calltranscription.index'), false);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CallTranscriptionSidebarTest`
Expected: FAIL — link not present under either condition yet.

- [ ] **Step 3: Add the gated sidebar entry**

In `resources/views/layouts/partials/sidebar.blade.php`, extend the existing `@php` block at the top (currently defines `$salereport_perm` and `$attendance_perm`) by adding one more line:

```php
$calltranscription_perm = App\Models\Permission::where('role_id', $user->role_id)->where('name', "calltranscription")->first();
```

Then append this new `<li>` right after the existing `@if(isset($attendance_perm) && $attendance_perm->view == 1) ... @endif` block (before the closing `</ul>`):

```blade
@if(isset($calltranscription_perm) && $calltranscription_perm->view == 1)
<li class="menu-item">
    <a href="{{ route('calltranscription.index') }}" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-microphone-message-outline"></i>
        <div data-i18n="Call Transcription">Call Transcription</div>
    </a>
</li>
@endif
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=CallTranscriptionSidebarTest`
Expected: PASS (both tests).

- [ ] **Step 5: Run the full test suite**

Run: `php artisan test`
Expected: all tests still PASS — confirms the sidebar edit didn't break any other page that renders it.

- [ ] **Step 6: Commit**

```bash
git add resources/views/layouts/partials/sidebar.blade.php tests/Feature/CallTranscriptionSidebarTest.php
git commit -m "feat: add gated Call Transcription sidebar entry"
```

---

### Task 7: Frontend UI — upload, animated processing, transcript viewer

**Files:**
- Modify: `resources/views/pages/callTranscription/index.blade.php` (replace the Task 4 placeholder with the full UI)

**Interfaces:**
- Consumes: routes `calltranscription.store`, `calltranscription.show`, `calltranscription.export`, `calltranscription.delete` (all from Tasks 4-5); `$agents` collection passed from `CallTranscriptionController::index()` (each item has `id`, `name`).
- No new backend interfaces — this task is UI-only.

This task has no automated test (no JS test runner in this stack) — verify manually in the browser per Task 8.

- [ ] **Step 1: Replace the placeholder view with the full UI**

Replace the entire contents of `resources/views/pages/callTranscription/index.blade.php` with:

```blade
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
```

- [ ] **Step 2: Manual verification**

This step has no automated test — verify by hand (Task 8 covers the full walkthrough). For now just confirm the page renders without a PHP error:

Run: `php artisan route:list --name=calltranscription`
Expected: lists `index`, `store`, `show`, `delete`, `export` routes, no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/views/pages/callTranscription/index.blade.php
git commit -m "feat: build call transcription upload, processing, and result UI"
```

---

### Task 8: End-to-end manual verification and regression check

**Files:** none (verification only).

- [ ] **Step 1: Grant yourself the permission for manual testing**

Since there's no seeder task in scope, grant the permission through the existing Roles & Permissions UI: log in as a role_id=1 (admin) user, go to Roles & Permissions → Users, note your role, go to the Roles list, edit your role, and check "Calltranscription" → View (and Create, Delete) → Save. (The "Calltranscription" checkbox will appear automatically once the new routes are registered, because `GlobalHelper::Permissions()` derives it from route names — no code change needed for it to appear.)

- [ ] **Step 2: Start the app and open the module in the browser**

Confirm the dev server / Laragon vhost is running, then navigate to `/call-transcription`. Confirm:
- Sidebar shows "Call Transcription" (only because permission was granted in Step 1).
- Upload zone responds to drag-over with the highlight animation.
- Selecting an unsupported file (e.g. a `.pdf`) shows the shake + red error message, doesn't enable Generate.
- Selecting a valid small audio file + picking an agent via the searchable dropdown enables "Generate Transcript".

- [ ] **Step 3: Run a real generation with an actual OpenAI key**

Ensure `.env`'s `OPENAI_API_KEY` is a real key (not touched by this plan — already existed as an empty slot). Upload a short real audio clip (a few seconds, one voice is fine for a smoke test), pick an agent, click Generate. Confirm:
- Upload progress bar advances.
- Waveform + rotating status messages appear.
- Result stage fades in with at least one turn, correct speaker label(s), duration/word/exchange metadata populated.
- Copy button copies text (paste somewhere to confirm) and shows the checkmark micro-animation.
- TXT, PDF, and DOCX export links each download a valid, openable file.
- "New Transcript" resets back to the upload stage cleanly.

- [ ] **Step 4: Confirm no audio was persisted**

Run:
```bash
find storage/app -newer composer.json -type f
```
Expected: no audio file present (only whatever unrelated files already existed, if any) — confirms nothing was written to disk during the generate request in Step 3.

- [ ] **Step 5: Regression check on existing pages**

Log in and click through: Dashboards (`home`), `AI Assistant` (`ai.index`), `Leads` → View/Create, `Sale` create page. Confirm each behaves exactly as before — no layout shift, no console errors, no broken links. This confirms the two additive edits (`routes/web.php`, `sidebar.blade.php`) introduced no regressions.

- [ ] **Step 6: Run the full automated test suite one last time**

Run: `php artisan test`
Expected: all tests PASS.

- [ ] **Step 7: Final review commit (if anything was tweaked during manual verification)**

If Steps 2-5 surfaced any fix-worthy issue, fix it, re-run the relevant automated test, then:
```bash
git add -A
git commit -m "fix: address issues found during manual verification"
```
If nothing needed fixing, skip this step — Task 7's commit is already the final state.
