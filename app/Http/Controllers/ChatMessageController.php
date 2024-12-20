<?php

namespace App\Http\Controllers;

use App\Services\ChatMessage\GetByChatRoomIdService;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    protected $getByRoomChatIdService;

    public function __construct(GetByChatRoomIdService $getByRoomChatIdService) 
    {
        $this->$getByRoomChatIdService = $getByRoomChatIdService;
    }
    public function getByChatRoomId($id)
    {

    }

    public function storeText(Request $request)
    {

    }

    public function storeSpeech(Request $request){

    }
}
