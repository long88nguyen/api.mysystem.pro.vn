<?php

namespace App\Services\ArtificialIntelligence;

use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use OpenAI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

            $languagesAllowed = ['english', 'vietnamese'];

            $fullPath = storage_path('app/'.$path);
            $apiKey = env('OPENAI_API_KEY', true);
            $client = OpenAI::client($apiKey);

            $response = $client->audio()->transcribe([
                'model' => 'whisper-1',
                'file' => fopen($fullPath, 'r'),
                'response_format' => 'verbose_json',
            ]);

            if($response && in_array($response->language, $languagesAllowed)) {
                return $this->sendSuccessResponse([
                    'text' => $response->text,
                    'language' => $response->language,
                    'url' => 'storage/audio/'.$imageName,
                ]);
            }
            return $this->sendErrorResponse('Bad request', ConstantService::HTTP_BAD_REQUEST);
        } else {
            return $this->sendErrorResponse('Bad request', ConstantService::HTTP_BAD_REQUEST);
        }
    }
}