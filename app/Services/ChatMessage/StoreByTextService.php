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
        $messages = [];
        $chatRoom = ChatRoom::findOrFail($request['chat_room_id']);
        $language = [
            'vi' => 'Tiếng Việt',
            'en' => 'Tiếng Anh',
        ];

        $botName = $chatRoom->bot_name ?? 'Bot Chat';
        $languageOutput =  $chatRoom->language && isset($language[$chatRoom->language]) ? $language[$chatRoom->language] : $language['en'];
        $requirements = $chatRoom->bot_description ? "" .$chatRoom->bot_description."" : "";
        
        $content = "Bạn tên là ".$botName.". ".$requirements."
        .Chỉ được giao tiếp bằng ".$languageOutput." ngắn gọn, dễ hiểu, 
        bạn phải trả lời bằng ".$languageOutput." 
        cho dù câu hỏi có là bất kỳ ngôn ngữ nào khác. Hãy hỏi sau khi chat từ: Bắt đầu";

        $messages[] = [
            'role' => 'system',
            'content' => $content,
        ];
        
        $this->chatMessageModel->create([
            'content' => $request['text'] ?? 'End',
            'chat_room_id' => $request['chat_room_id'],
            'role' => 'user',
            'user_id' => auth(ConstantService::AUTH_USER)->user()->id,
            'audio' => isset($request['audio']) ? $request['audio'] : null,
        ]);

        $historyMessage = $this->chatMessageModel->where('chat_room_id', $request['chat_room_id'])->orderBy('created_at', 'asc')->get(['content', 'role'])->toArray();
        foreach($historyMessage as $message)
        {
            $messages[] = [
                'role' => $message['role'],
                'content' => $message['content'],
            ];
        }

        $chatGPTOptions = [
            'model' => $chatRoom->chat_gpt_model ?? 'gpt-3.5-turbo-0125',
            'messages' => $messages,
            'bot_name' => $chatRoom->bot_name,
            'bot_description' => $chatRoom->bot_description,
        ];

        $chatGPTResult = $this->chatGPT->chat($chatGPTOptions);

        $translationOptions = [
            'language' => $chatRoom->language == 'vi' ? 'en' : 'vi',
            'text' => $chatGPTResult,
        ];

        $chatGPTTranstionResult = $this->chatGPT->translation($translationOptions);

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
            'role' => 'assistant',
            'user_id' => auth(ConstantService::AUTH_USER)->user()->id,
            'audio' => $textToSpeechResult,
            'translation' => $chatGPTTranstionResult,
        ]);

        return $this->sendSuccessResponse($result);
    }
}   