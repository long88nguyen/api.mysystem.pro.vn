<?php

namespace App\Services\ArtificialIntelligence;

use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Exception;
use Illuminate\Support\Facades\Storage;
use OpenAI;

class ConvertSpeechToTextService extends BaseService
{
    protected $messageModel;

    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    public function convert($request)
    {
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');
            $imageName = time() . '.' . $audioFile->getClientOriginalExtension();
            $path = $audioFile->storeAs('public/audio', $imageName);
            $apiPath = Storage::disk('public')->url('audio/'.$imageName);
            $fullPath = storage_path('app/'.$path);
            $apiKey = env('OPENAI_API_KEY', true);
            $client = OpenAI::client($apiKey);

            try {
                $response = $client->audio()->transcribe([
                    'model' => 'whisper-1',
                    'file' => fopen($fullPath, 'r'),
                    'response_format' => 'verbose_json',
                    'language' => 'en', // Chỉ nhận diện tiếng Anh
                ]);
                // Kiểm tra xem ngôn ngữ có phải là tiếng Anh không và độ tin cậy của kết quả
                if ($response && $response->language === 'english') {
                    $logProb = $response->segments[0]->avgLogprob ?? null; // Giá trị độ tin cậy
                    $noSpeechProb = $response->segments[0]->noSpeechProb ?? null;
                    if ($logProb !== null && $logProb > -0.7 && $noSpeechProb < 0.5) { // Điều chỉnh ngưỡng tùy theo yêu cầu độ chính xác
                        return [
                            'text' => $response->text,
                            'url' => $apiPath,
                        ];
                    }
                }
        
                // Trường hợp không đạt yêu cầu
                return false;
            } catch (Exception $e) {
                return false;
            }
        } else {
            return $this->sendErrorResponse('Vui lòng phát âm lại');
        }
    }
}