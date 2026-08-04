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
     *
     * Stores compliance_turns as an array of HTML strings, index-aligned
     * with $record->transcript_json, so the highlighted version of each
     * turn can be swapped in for its plain text wherever the transcript is
     * already rendered -- rather than producing a second, separate copy
     * of the transcript.
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

        $numberedTranscript = $this->renderTurnsForPrompt($turns);

        $record->update(['compliance_status' => 'processing']);

        $attempt = 0;

        while (true) {
            try {
                $response = OpenAI::chat()->create([
                    'model' => self::ANALYSIS_MODEL,
                    'temperature' => 0,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt(count($turns))],
                        ['role' => 'user', 'content' => $numberedTranscript],
                    ],
                ]);

                $decoded = json_decode($response->choices[0]->message->content, true);

                $turnsOut = $decoded['turns'] ?? null;
                $counts = $decoded['counts'] ?? null;

                if (!is_array($turnsOut) || count($turnsOut) !== count($turns) || !is_array($counts)) {
                    throw new \RuntimeException(sprintf(
                        'Compliance response shape mismatch: expected %d turns, got %s.',
                        count($turns),
                        is_array($turnsOut) ? count($turnsOut) : gettype($turnsOut)
                    ));
                }

                $normalizedCounts = [];
                foreach (self::COUNT_KEYS as $key) {
                    $normalizedCounts[$key] = (int) ($counts[$key] ?? 0);
                }

                $record->update([
                    'compliance_status' => 'completed',
                    'compliance_turns' => array_values($turnsOut),
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

        foreach (array_values($turns) as $index => $turn) {
            $label = $turn['speaker_label'] ?? ($turn['speaker'] ?? 'Unknown');
            $text = $turn['text'] ?? '';
            $lines[] = "[{$index}] {$label}: {$text}";
        }

        return implode("\n", $lines);
    }

    private function systemPrompt(int $turnCount): string
    {
        // Verbatim rule set as provided. The output contract is changed from
        // "one flat HTML transcript plus a compact summary" to a JSON array
        // of per-turn highlighted strings, index-aligned with the app's own
        // transcript_json. A flat re-rendered transcript has no reliable way
        // to be spliced back into the existing turn-by-turn UI/exports
        // without duplicating the whole conversation a second time; per-turn
        // output lets the highlighted text replace the plain text of the
        // same turn in place.
        return <<<PROMPT
            You are a sales-call compliance auditor. You will be given a numbered list of transcript turns, each already labeled with the index and speaker who said it. Identify which speaker is the Closer/Salesperson and which is the Customer.

            For every turn, in the same order and at the same index, return that turn's text unchanged in wording and order, with the smallest relevant phrases wrapped in highlight spans per the rules below. Do not add, remove, merge, split, or reorder turns. Do not include the "[index] Speaker:" prefix in your output -- return only the turn's own text (highlighted).

            Escape any literal <, >, or & characters that appear in the turn's own text as &lt; &gt; &amp; before adding your span/strong tags around any part of it.

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
              "turns": [
                "<turn 0's own text, highlighted>",
                "<turn 1's own text, highlighted>",
                ... exactly {$turnCount} entries total, same order as given ...
              ],
              "counts": {
                "red_violations": <int>,
                "yellow_review_items": <int>,
                "prices_below_150": <int>,
                "position_commitments": <int>,
                "timeframe_violations": <int>
              }
            }
            The turns array must contain exactly {$turnCount} entries, one per input turn, in the same order.
            PROMPT;
    }
}
