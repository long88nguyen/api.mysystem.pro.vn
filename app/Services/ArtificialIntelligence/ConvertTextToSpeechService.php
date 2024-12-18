<?php

namespace App\Services\ArtificialIntelligence;

use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use OpenAI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ConvertTextToSpeechService extends BaseService
{
    public function convert($request)
    {
        $apiKey = env('OPEN_AI_KEY', true);

        $response = $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/tts', [
            'model' => 'text-to-speech-1',
            'input' => 'Your text goes here',
            'voice' => 'default', // Select from available voices
            'format' => 'mp3',
        ]);

        dd($response->json('audio_url'));
    }
}