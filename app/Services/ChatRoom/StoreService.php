<?php

namespace App\Services\ChatRoom;

use App\Models\ChatRoom;
use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use OpenAI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class StoreService extends BaseService
{
    protected $chatRoomModel;

    public function __construct(ChatRoom $chatRoomModel) 
    {
        $this->chatRoomModel = $chatRoomModel;
    }

    public function store($request)
    {
        $data = $request->all();

        $arraySave = [
            "bot_name" => $data['bot_name'],
            "bot_avatar" => $data['bot_avatar'],
            "bot_description" => $data['bot_description'],
            "user_id" => auth(ConstantService::AUTH_USER)->user()->id,
            "name" => $data['name'],
            "text_to_speech_model" => $data['text_to_speech_model'],
            "voice_model" => $data['voice_model'],
            "speech_to_text_model" => $data['speech_to_text_model'],
            "chat_gpt_model" => $data['chat_gpt_model'],
            "language" => $data['language'],
            "created_at" => Carbon::now(),
        ];

        $result = $this->chatRoomModel->create($arraySave);
        return $this->sendSuccessResponse($result);
    }
}