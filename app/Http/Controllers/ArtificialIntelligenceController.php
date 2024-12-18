<?php

namespace App\Http\Controllers;

use App\Services\ArtificialIntelligence\ConvertSpeechToTextService;
use App\Services\ArtificialIntelligence\ConvertTextToSpeechService;
use Illuminate\Http\Request;

class ArtificialIntelligenceController extends Controller
{
    protected $convertSpeechToText;
    protected $convertTextToSpeech;

    public function __construct(ConvertSpeechToTextService $convertSpeechToText, ConvertTextToSpeechService $convertTextToSpeech)
    {
        $this->convertSpeechToText = $convertSpeechToText;
        $this->convertTextToSpeech = $convertTextToSpeech;
    }

    public function convert(Request $request)
    {
        return $this->convertSpeechToText->convert($request);
    }

    public function convertTextToSpeech(Request $request)
    {
        return $this->convertTextToSpeech->convert($request);
    }
}
