<?php

namespace App\Services;

use App\Models\CallTranscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
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

        if ($record->compliance_status === 'completed' && $record->compliance_summary) {
            $lines[] = str_repeat('-', 40);
            $lines[] = 'COMPLIANCE SUMMARY';
            $lines[] = str_repeat('-', 40);
            foreach ($record->compliance_summary as $label => $count) {
                $lines[] = ucwords(str_replace('_', ' ', $label)) . ': ' . $count;
            }
            $lines[] = '';
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transcript-' . $record->uuid . '.txt"',
        ]);
    }

    public function toPdf(CallTranscription $record)
    {
        return Pdf::loadView('pages.callTranscription.exports.pdf', [
            'record' => $record,
            'complianceHtml' => $record->compliance_status === 'completed' ? $record->compliance_html : null,
            'complianceSummary' => $record->compliance_status === 'completed' ? $record->compliance_summary : null,
        ])->download('transcript-' . $record->uuid . '.pdf');
    }

    public function toDocx(CallTranscription $record): BinaryFileResponse
    {
        $phpWordTempDir = storage_path('app/tmp/phpword');
        if (!is_dir($phpWordTempDir)) {
            mkdir($phpWordTempDir, 0755, true);
        }
        // PhpWord builds the .docx in a scratch subfolder under its temp dir, then
        // deletes it via scandir(). Left at the default sys_get_temp_dir(), that
        // resolves to C:\Windows\Temp on this machine, which the web server's
        // account can't fully read/write/delete in. Pointing it at a folder inside
        // our own storage directory avoids that permissions problem entirely.
        Settings::setTempDir($phpWordTempDir);

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

        if ($record->compliance_status === 'completed' && $record->compliance_summary) {
            $section->addTextBreak(1);
            $section->addText('Compliance Summary', ['bold' => true, 'size' => 14]);
            foreach ($record->compliance_summary as $label => $count) {
                $section->addText(ucwords(str_replace('_', ' ', $label)) . ': ' . $count);
            }
        }

        $tempPath = tempnam($phpWordTempDir, 'docx');
        IOFactory::createWriter($phpWord, 'Word2007')->save($tempPath);

        return response()->download($tempPath, 'transcript-' . $record->uuid . '.docx')->deleteFileAfterSend(true);
    }
}
