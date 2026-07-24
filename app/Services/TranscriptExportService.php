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
