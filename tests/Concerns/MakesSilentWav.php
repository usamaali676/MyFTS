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
