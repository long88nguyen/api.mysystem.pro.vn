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
            $languagesAllowed = ['english', 'vietnamese'];

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
                    $logProb = $response->segments[0]->avg_log_prob ?? null; // Giá trị độ tin cậy
                    if ($logProb !== null && $logProb > -1.0) { // Điều chỉnh ngưỡng tùy theo yêu cầu độ chính xác
                        return [
                            'text' => $response->text,
                            'url' => $apiPath,
                        ];
                    }
                }
        
                // Trường hợp không đạt yêu cầu
                return [
                    'text' => 'Kết quả không đạt yêu cầu độ chính xác.',
                    'url' => null,
                ];
            } catch (Exception $e) {
                return [
                    'text' => 'Lỗi xảy ra khi xử lý âm thanh: ' . $e->getMessage(),
                    'url' => null,
                ];
            }
        } else {
            return $this->sendErrorResponse('Vui lòng phát âm lại');
        }
    }
}