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
    protected $apiUrl;
    protected $apiKey;

    public function __construct() {}

    public function convert($request)
    {
        $languages = [ 'en' => 'Tiếng Anh', 'vi' => 'Tiếng Việt'];
        $chatGPTPrompt = new ChatGPTPromptSevice();
        $languageSelect = $request['language'] ?? 'vi';
        $resultChatGPTPrompt = $chatGPTPrompt->chat($request['text'], $languages[$languageSelect]);
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
            'model' => isset($request['model']) ? $request['model'] : 'tts-1',
            'input' => $resultChatGPTPrompt ?? 'Chúng tôi không hiểu bạn nói gì',
            'voice' => isset($request['voice']) ? $request['voice'] : 'alloy',
            'language' => isset($request['language']) ? $request['language'] : 'en',
        )));

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            return $this->sendErrorResponse('Không ghi được file âm thanh');
        } else {
            // Tên file ngẫu nhiên để tránh ghi đè
            $filename = 'speech_' . time() . '.mp3';

            // Sử dụng Storage của Laravel để lưu file vào storage/app/public/audio/
            Storage::disk('public')->put($filename, $result);

            // $publicUrl = asset('storage/' . $filename);

            $publicUrl = Storage::disk('public')->url($filename);

            return $this->sendSuccessResponse([
                'url' => $publicUrl,
                'text' => $resultChatGPTPrompt,
            ]);
        }

        curl_close($ch);
    }
}
