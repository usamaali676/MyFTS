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
