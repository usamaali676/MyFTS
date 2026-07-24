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
