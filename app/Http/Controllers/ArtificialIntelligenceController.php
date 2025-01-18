<?php

namespace App\Http\Controllers;

use App\Services\ArtificialIntelligence\ChatGPTPromptSevice;
use App\Services\ArtificialIntelligence\ConvertSpeechToTextService;
use App\Services\ArtificialIntelligence\ConvertTextToSpeechService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtificialIntelligenceController extends Controller
{
    protected $convertSpeechToText;
    protected $convertTextToSpeech;
    protected $chatGPTPromptSerivce; 

    public function __construct(ConvertSpeechToTextService $convertSpeechToText, 
    ConvertTextToSpeechService $convertTextToSpeech,
    ChatGPTPromptSevice $chatGPTPromptSerivce
    )
    {
        $this->convertSpeechToText = $convertSpeechToText;
        $this->convertTextToSpeech = $convertTextToSpeech; 
        $this->chatGPTPromptSerivce = $chatGPTPromptSerivce;
    }

    public function convert(Request $request)
    {
        return $this->convertSpeechToText->convert($request);
    }

    public function convertTextToSpeech(Request $request)
    {
        $request->validate([
            'text' => 'required',
        ]);

        return $this->convertTextToSpeech->convert($request);
    }

    public function chatGPTPrompt(Request $request)
    {
        $request->validate([
            'prompt' => 'required',
        ]);

        return $this->chatGPTPromptSerivce->chat($request['prompt'], $request['language'] ?? 'vi');
    }
}