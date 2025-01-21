<?php

namespace App\Services\ArtificialIntelligence;

use App\Services\_Abstract\BaseService;
use Illuminate\Support\Facades\Storage;

class ConvertTextToSpeechService extends BaseService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct() {}

    public function convert($options)
    {
        $openai_api_key = env('OPENAI_API_KEY');
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/audio/speech');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $openai_api_key,
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            // 'model' => isset($request['model']) ? $request['model'] : 'tts-1',
            // 'input' => $resultChatGPTPrompt ?? 'Chúng tôi không hiểu bạn nói gì',
            // 'voice' => isset($request['voice']) ? $request['voice'] : 'alloy',
            // 'language' => isset($request['language']) ? $request['language'] : 'en',

            'model' => $options['model'] ?? 'tts-1',
            'input' => $options['input'] ?? 'Chúng tôi không hiểu bạn nói gì',
            'voice' => $options['voice'] ?? 'alloy',
            'language' => $options['language'] ?? 'en',
        )));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        } else {
            // Tên file ngẫu nhiên để tránh ghi đè
            $filename = 'speech_' . time() . '.mp3';

            // Sử dụng Storage của Laravel để lưu file vào storage/app/public/audio/
            Storage::disk('public')->put($filename, $result);

            // $publicUrl = asset('storage/' . $filename);

            $publicUrl = Storage::disk('public')->url($filename);

            return $publicUrl;
        }

        curl_close($ch);
    }
}
