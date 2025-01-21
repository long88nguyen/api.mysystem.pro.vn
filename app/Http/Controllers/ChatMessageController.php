<?php

namespace App\Http\Controllers;

use App\Models\Pronunciation;
use App\Services\ArtificialIntelligence\ConvertTextToSpeechService;
use App\Services\ChatMessage\GetByChatRoomIdService;
use App\Services\ChatMessage\StoreBySpeechService;
use App\Services\ChatMessage\StoreByTextService;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    protected $getByRoomChatIdService;
    protected $storeByText;
    protected $storeBySpeech;

    public function __construct( 
    StoreByTextService $storeByText,
    StoreBySpeechService $storeBySpeech
    ) 
    {
        $this->storeByText = $storeByText;
        $this->storeBySpeech = $storeBySpeech;
    }
    public function getByChatRoomId($id)
    {

    }

    public function storeText(Request $request)
    {
       return $this->storeByText->store($request);
    }

    public function storeSpeech(Request $request){
        return $this->storeBySpeech->store($request);
    }
}
