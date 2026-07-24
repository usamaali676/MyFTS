<?php

namespace App\Http\Controllers;

use App\Exceptions\TranscriptionFailedException;
use App\Http\Requests\GenerateTranscriptRequest;
use App\Models\CallTranscription;
use App\Models\User;
use App\Services\CallTranscriptionService;
use App\Services\TranscriptExportService;
use Illuminate\Support\Facades\Auth;

class CallTranscriptionController extends Controller
{
    public function __construct(
        private readonly CallTranscriptionService $service,
        private readonly TranscriptExportService $exportService,
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
