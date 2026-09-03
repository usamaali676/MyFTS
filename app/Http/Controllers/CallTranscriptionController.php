<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateTranscriptRequest;
use App\Models\CallTranscription;
use App\Models\Role;
use App\Models\User;
use App\Services\CallTranscriptionService;
use App\Services\TranscriptComplianceService;
use App\Services\TranscriptExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CallTranscriptionController extends Controller
{
    public function __construct(
        private readonly CallTranscriptionService $service,
        private readonly TranscriptExportService $exportService,
        private readonly TranscriptComplianceService $complianceService,
    ) {}

    public function index()
    {
        $roles = Role::whereIn('name', ['TSR', 'Customer Support', 'Manager Customer Support', 'Closer'])->pluck('id');
        $agents = User::whereIn('role_id', $roles)->orderBy('name')->get(['id', 'name']);

        return view('pages.callTranscription.index', compact('agents'));
    }

    public function store(GenerateTranscriptRequest $request)
    {

        $agent = User::findOrFail($request->integer('agent_user_id'));

        $audio = $request->file('audio');

        if (!$audio || !$audio->isValid()) {
            $uploadError = $audio?->getError();

            Log::warning('Call transcription upload rejected before processing', [
                'upload_error_code' => $uploadError,
                'upload_error_label' => $audio ? $audio->getErrorMessage() : 'no file present',
                'php_ini_upload_max_filesize' => ini_get('upload_max_filesize'),
                'php_ini_post_max_size' => ini_get('post_max_size'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The file failed to upload. This is usually caused by the recording exceeding the server\'s upload size limit, not by the recording itself. Please contact support if this keeps happening.',
            ], 422);
        }

        $record = $this->service->generate(
            $request->file('audio'),
            $agent,
            (string) $request->string('request_uuid'),
            Auth::id(),
        );

        return response()->json(['success' => true, 'data' => $this->transformRecord($record)]);
    }

    public function show(string $uuid)
    {
        $record = CallTranscription::where('uuid', $uuid)->firstOrFail();

        return response()->json(['success' => true, 'data' => $this->transformRecord($record)]);
    }

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

    public function analyzeCompliance(string $uuid)
    {
        $record = CallTranscription::where('uuid', $uuid)->firstOrFail();

        if ($record->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'The transcript must finish processing before it can be audited.',
            ], 422);
        }

        $this->complianceService->analyze($record);
        $record = $record->fresh();

        return response()->json([
            'success' => $record->compliance_status === 'completed',
            'compliance_status' => $record->compliance_status,
            'compliance_turns' => $record->compliance_turns,
            'compliance_summary' => $record->compliance_summary,
            'message' => $record->compliance_error,
        ]);
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
            'error_message' => $record->error_message,
            'turns' => $record->transcript_json ?? [],
            'compliance_status' => $record->compliance_status,
            'compliance_turns' => $record->compliance_turns,
            'compliance_summary' => $record->compliance_summary,
            'compliance_error' => $record->compliance_error,
        ];
    }
}
