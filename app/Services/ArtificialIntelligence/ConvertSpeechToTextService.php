<?php

namespace App\Services\ArtificialIntelligence;

use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Exception;
use getID3;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use OpenAI;
use FFMpeg\FFMpeg;

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
                    'temperature' => 0
                ]);

                // dd($response->segments[0]);
                // Kiểm tra xem ngôn ngữ có phải là tiếng Anh không và độ tin cậy của kết quả
                if ($response && $response->language === 'english') {
                    $logProb = $response->segments[0]->avgLogprob ?? null; // Giá trị độ tin cậy
                    $noSpeechProb = $response->segments[0]->noSpeechProb ?? null;
                    if ($logProb !== null && $logProb > -1.0 && $noSpeechProb < 0.5) { // Điều chỉnh ngưỡng tùy theo yêu cầu độ chính xác
                        return [
                            'text' => $response->text,
                            'url' => $apiPath,
                        ];
                    }
                }
        
                // Trường hợp không đạt yêu cầu
                return [
                    'text' => null,
                    'url' => null,
                ];
            } catch (Exception $e) {
                return [
                    'text' => null,
                    'url' => null,
                ];;
            }
        } else {
            return $this->sendErrorResponse('Vui lòng phát âm lại');
        }
    }

    public function convertGoogleCloud($request)
    {
        $audioFile = $request->file('audio');
        $imageName = time() . '.' . 'mp3';
        $path = $audioFile->storeAs('public/audio', $imageName);
        $apiPath = Storage::disk('public')->url('audio/'.$imageName);

        $audioContent = file_get_contents($request->file('audio')->getRealPath());
        $googleCloudAPIKey = 'AIzaSyADhpMrZ95PS9lQEjj37ODczKgRFe5oFuU';
        $url = 'https://speech.googleapis.com/v1p1beta1/speech:recognize';

        $requestData = [
            'config' => [
                'encoding' => 'LINEAR16',
                // 'sampleRateHertz' => 16000,
                'languageCode' => 'en-US',
            ],
            'audio' => [
                'content' => base64_encode($audioContent),
            ],
        ];

        try {
            $client =  new Client();
            $response = $client->post($url, [
                'query' => ['key' => $googleCloudAPIKey],
                'json' => $requestData,
            ]);
            $responseData = json_decode($response->getBody(), true);
            if (isset($responseData['results'][0]['alternatives'][0]['transcript'])) {
                return [                   
                    'text' => $responseData['results'][0]['alternatives'][0]['transcript'],
                    'url' => $apiPath,
                ];
            } else {
                return [
                    'text' => 'Không có dữ liệu',
                    'url' => $apiPath,
                ];
            }
        } catch (RequestException $e) {
            return [
                'text' => "Error: ".$e->getMessage(),
                'url' => $apiPath,
            ];
        }
    }
}