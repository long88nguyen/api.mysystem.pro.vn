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

class StoreByTextService extends BaseService
{
    protected $chatMessageModel;
    protected $textToSpeech;
    protected $speechToText;
    protected $chatGPT;

    public function __construct(ChatMessage $chatMessageModel, 
        ConvertTextToSpeechService $textToSpeech, 
        ConvertSpeechToTextService $speechToText,
        ChatGPTPromptSevice $chatGPT) 
    {
        $this->chatMessageModel = $chatMessageModel;
        $this->textToSpeech = $textToSpeech;
        $this->speechToText = $speechToText;
        $this->chatGPT = $chatGPT;
    }

    public function store($request)
    {
        $this->chatMessageModel->create([
            'content' => $request['text'],
            'chat_room_id' => $request['chat_room_id'],
            'role' => 'user',
            'user_id' => auth(ConstantService::AUTH_USER)->user()->id,
        ]);


        $chatRoom = ChatRoom::findOrFail($request['chat_room_id']);

        $chatGPTOptions = [
            'language' => $chatRoom->language ?? 'vi',
            'prompt' => $request['text'],
        ];
        $chatGPTResult = $this->chatGPT->chat($chatGPTOptions);

        $textToSpeechOptions = [
            'model' => $chatRoom->text_to_speech_model,
            'voice' => $chatRoom->voice_model,
            'language' => $chatRoom->language,
            'input' => $chatGPTResult,
        ];

        $textToSpeechResult = $this->textToSpeech->convert($textToSpeechOptions); 

        $result = $this->chatMessageModel->create([
            'content' => $chatGPTResult,
            'chat_room_id' => $request['chat_room_id'],
            'role' => 'system',
            'user_id' => auth(ConstantService::AUTH_USER)->user()->id,
            'audio' => $textToSpeechResult
        ]);

        return $this->sendSuccessResponse($result);
    }
}