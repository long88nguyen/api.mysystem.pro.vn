<?php

namespace App\Services\ChatRoom;

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

class DeleteService extends BaseService
{
    protected $chatRoomModel;

    public function __construct(ChatRoom $chatRoomModel) 
    {
        $this->chatRoomModel = $chatRoomModel;
    }

    public function delete($id)
    {
        $this->chatRoomModel->findOrFail($id)->delete();
        ChatMessage::where('chat_room_id', $id)->delete();

        return $this->sendSuccessResponse([]);
    }
}