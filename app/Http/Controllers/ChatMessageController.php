<?php

namespace App\Http\Controllers;

use App\Services\ChatMessage\GetByChatRoomIdService;
use App\Services\ChatMessage\StoreByTextService;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    protected $getByRoomChatIdService;
    protected $storeByText;

    public function __construct( 
    StoreByTextService $storeByText) 
    {
        $this->storeByText = $storeByText;
    }
    public function getByChatRoomId($id)
    {

    }

    public function storeText(Request $request)
    {
       return $this->storeByText->store($request);
    }

    public function storeSpeech(Request $request){

    }
}
