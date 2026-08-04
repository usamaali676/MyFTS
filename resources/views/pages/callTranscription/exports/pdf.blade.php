<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #2e263d; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 8px; }
        .compliance-meta { color: #666; margin-bottom: 16px; font-size: 11px; }
        .compliance-meta b { color: #2e263d; }
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

    @php
        $complianceTurns = ($record->compliance_status === 'completed') ? ($record->compliance_turns ?? []) : [];
        $complianceSummary = ($record->compliance_status === 'completed') ? $record->compliance_summary : null;
    @endphp

    @if ($complianceSummary)
        <div class="compliance-meta">
            Compliance audit:
            @foreach ($complianceSummary as $label => $count)
                <b>{{ $count }}</b> {{ ucwords(str_replace('_', ' ', $label)) }}@if (!$loop->last) &middot; @endif
            @endforeach
        </div>
    @endif

    @foreach ($record->transcript_json ?? [] as $idx => $turn)
        <div class="turn">
            <div class="ts">[{{ $turn['timestamp_label'] ?? '--:--:--' }}]</div>
            <div class="speaker">{{ $turn['speaker_label'] }}:</div>
            {{-- $complianceTurns[$idx] is model-generated markup (span/strong tags
                 with inline background-color only) built from this exact turn's
                 own text, not third-party or user-submitted HTML. Falls back to
                 the plain escaped text when no analysis has run yet. --}}
            <div>
                @if (isset($complianceTurns[$idx]))
                    {!! $complianceTurns[$idx] !!}
                @else
                    {{ $turn['text'] }}
                @endif
            </div>
        </div>
    @endforeach
</body>
</html>
