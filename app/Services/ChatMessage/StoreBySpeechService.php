<?php

namespace App\Services\ChatMessage;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use App\Services\ArtificialIntelligence\ChatGPTPromptSevice;
use App\Services\ArtificialIntelligence\ConvertSpeechToTextService;
use App\Services\ArtificialIntelligence\ConvertTextToSpeechService;
use GuzzleHttp\Client;
use OpenAI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StoreBySpeechService extends BaseService
{
    protected $convertSpeechToTextService;
    protected $chatMessageModel;
    public function __construct(ConvertSpeechToTextService $convertSpeechToTextService,ChatMessage $chatMessageModel)
    {
        $this->convertSpeechToTextService = $convertSpeechToTextService;
        $this->chatMessageModel = $chatMessageModel;
    }

    public function store($request)
    {
        $convertSpeectToTextResult =  $this->convertSpeechToTextService->convert($request);
        $result =  [
            'content' => $convertSpeectToTextResult['text'],
            'audio' => $convertSpeectToTextResult['url'],
        ];

        return $this->sendSuccessResponse($result);
    }
}