<?php

namespace App\Services;

use App\Models\CallTranscription;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class TranscriptComplianceService
{
    // Reasoning-heavy rule application (numeric thresholds, "who said it",
    // "was this a rejected commitment") is exactly where -mini models are
    // more likely to mis-highlight. Use the larger model here even though
    // diarization elsewhere uses gpt-4o-mini for plain labeling.
    private const ANALYSIS_MODEL = 'gpt-4o';
    private const MAX_RETRIES = 2;

    private const COUNT_KEYS = [
        'red_violations',
        'yellow_review_items',
        'prices_below_150',
        'position_commitments',
        'timeframe_violations',
    ];

    /**
     * Run the compliance audit against an already-transcribed/diarized call
     * and persist the result onto the record. Never throws -- failures are
     * recorded on the model itself so a bad analysis never takes down an
     * otherwise-successful transcription.
     */
    public function analyze(CallTranscription $record): void
    {
        $turns = $record->transcript_json ?? [];

        if (count($turns) === 0) {
            $record->update([
                'compliance_status' => 'failed',
                'compliance_error' => 'No transcript turns are available to analyze.',
            ]);

            return;
        }

        $transcriptText = $this->renderTurnsForPrompt($turns);

        $record->update(['compliance_status' => 'processing']);

        $attempt = 0;

        while (true) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => self::ANALYSIS_MODEL,
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => $transcriptText],
                    ],
                ]);

                $decoded = json_decode($response->choices[0]->message->content, true);

                $html = $decoded['html'] ?? null;
                $counts = $decoded['counts'] ?? null;

                if (!is_string($html) || $html === '' || !is_array($counts)) {
                    throw new \RuntimeException('Compliance response was missing the expected html/counts fields.');
                }

                $normalizedCounts = [];
                foreach (self::COUNT_KEYS as $key) {
                    $normalizedCounts[$key] = (int) ($counts[$key] ?? 0);
                }

                $record->update([
                    'compliance_status' => 'completed',
                    'compliance_html' => $html,
                    'compliance_summary' => $normalizedCounts,
                    'compliance_error' => null,
                ]);

                return;
            } catch (\OpenAI\Exceptions\RateLimitException|\OpenAI\Exceptions\ServerException|\OpenAI\Exceptions\TransporterException $e) {
                if (++$attempt > self::MAX_RETRIES) {
                    Log::error('Compliance analysis failed after max retries', [
                        'call_transcription_id' => $record->id,
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'attempts' => $attempt,
                    ]);

                    $record->update([
                        'compliance_status' => 'failed',
                        'compliance_error' => 'The compliance analysis service is temporarily unavailable. Please try again shortly.',
                    ]);

                    return;
                }
                usleep(300000 * $attempt);
            } catch (\OpenAI\Exceptions\ErrorException $e) {
                Log::error('Compliance analysis rejected by OpenAI', [
                    'call_transcription_id' => $record->id,
                    'message' => $e->getMessage(),
                ]);

                $record->update([
                    'compliance_status' => 'failed',
                    'compliance_error' => 'The compliance analysis service rejected the request: ' . $e->getMessage(),
                ]);

                return;
            } catch (\Throwable $e) {
                Log::error('Compliance analysis failed unexpectedly', [
                    'call_transcription_id' => $record->id,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                $record->update([
                    'compliance_status' => 'failed',
                    'compliance_error' => 'An unexpected error occurred while analyzing the transcript.',
                ]);

                return;
            }
        }
    }

    /**
     * @param  array<int, array{speaker_label: string, text: string}>  $turns
     */
    private function renderTurnsForPrompt(array $turns): string
    {
        $lines = [];

        foreach ($turns as $turn) {
            $label = $turn['speaker_label'] ?? ($turn['speaker'] ?? 'Unknown');
            $text = $turn['text'] ?? '';
            $lines[] = "{$label}: {$text}";
        }

        return implode("\n", $lines);
    }

    private function systemPrompt(): string
    {
        // Verbatim rule set as provided, with the output contract changed from
        // "HTML transcript then a compact summary" (free text) to a single
        // strict JSON object. Free-text output would require fragile string
        // splitting to separate the transcript from the counts on our end;
        // JSON keeps both pieces machine-readable so the app can render the
        // HTML and display the counts independently without re-parsing prose.
        return <<<PROMPT
            You are a sales-call compliance auditor. Analyze the transcript, identify the Closer/Salesperson and Customer, preserve the exact wording, order, and speaker labels, and produce an HTML-highlighted transcript plus a compact violation summary. Highlight the smallest relevant phrase.

            Color priority: RED > YELLOW > SKY.

            RED #ffcccc
            Any closer commitment, guarantee, target, estimate, or expected future result containing a specific number of customers, keywords, reviews, rankings, clicks, calls, leads, jobs, sales, revenue, money, or other measurable outcomes.
            Any promised specific Google position, including #1, first position, top 3, map-pack position, or any numbered/fixed position on the first page.
            Any guaranteed result or guaranteed timeframe.
            Organic/local SEO/Google Business Profile timing outside 20-35 days.
            Website SEO timing outside 75-105 days.
            Any closer-quoted service price below \$150; make it bold and red.

            YELLOW #fff2cc
            Customer questions about prices, customers, keywords, rankings, positions, visibility, clicks, calls, leads, results, or timeframes.
            Free services, trials, months, setup, or other free offers made by the closer.
            Closer-quoted service prices of \$150 or more.
            Organic/local SEO timing of 20-24 or 31-35 days.
            Website SEO timing of 75-84 or 96-105 days.
            Statements where the speaker or intent is unclear.

            SKY #d9eaf7
            Services offered or explained.
            Locations and service areas.
            AI-related discussion, including ChatGPT, Gemini, Google AI, AI Mode, or similar tools.

            Allowed without red highlighting:
            General discussion of customers, keywords, rankings, calls, leads, visibility, reviews, or results.
            Broad statements such as "top of Google," "improve rankings," "increase visibility," "get more calls," or "reach the first page of Google," provided no specific position, number, guarantee, or prohibited timeframe is committed.
            Organic/local SEO estimates of 25-30 days.
            Website SEO estimates of approximately 85-95 days.
            Only flag future commitments made by the closer for this customer. Do not flag customer statements, past results, examples, hypothetical scenarios, comparisons, or rejected commitments such as "we cannot guarantee first position."
            Words such as "aim," "target," "expect," "should," "approximately," or "up to" do not prevent a specific measurable commitment from being red.
            Apply the price rule only to the closer's service fee, not ad budgets, customer budgets, revenue, past spending, or unrelated amounts. Apply the \$150 threshold only to USD unless a USD equivalent is stated.

            Use:
            <span style="background-color:#ffcccc">...</span>
            <span style="background-color:#fff2cc">...</span>
            <span style="background-color:#d9eaf7">...</span>
            For prices below \$150:
            <strong><span style="background-color:#ffcccc">...</span></strong>

            Respond with strict JSON only, no prose, no markdown fences, in exactly this shape:
            {
              "html": "<the full HTML-highlighted transcript, preserving speaker labels and line order, as a single string with <br> or <p> between turns>",
              "counts": {
                "red_violations": <int>,
                "yellow_review_items": <int>,
                "prices_below_150": <int>,
                "position_commitments": <int>,
                "timeframe_violations": <int>
              }
            }
            PROMPT;
    }
}
