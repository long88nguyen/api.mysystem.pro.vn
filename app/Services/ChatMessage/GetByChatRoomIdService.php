<?php

namespace App\Services\ChatMessage;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use GuzzleHttp\Client;
use OpenAI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GetByChatRoomIdService extends BaseService
{
    protected $chatMessageModel;

    public function __construct(ChatMessage $chatMessageModel) 
    {
        $this->chatMessageModel = $chatMessageModel;
    }

    public function getAll($roomChatId)
    {
        $chatMessages = $this->chatMessageModel->where('room_chat_id', $roomChatId)->orderBy('created_at', 'desc')->get();
        return $this->sendSuccessResponse($chatMessages);
    }
}