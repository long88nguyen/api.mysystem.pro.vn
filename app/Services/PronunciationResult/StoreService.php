<?php

namespace App\Services\PronunciationResult;

use App\Models\Message;
use App\Models\Pronunciation;
use App\Models\PronunciationDetail;
use App\Models\PronunciationResult;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use App\Services\ArtificialIntelligence\ConvertSpeechToTextService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreService extends BaseService
{
    protected $pronunciationResultModel;
    protected $convertSpeechToText;

    public function __construct(
        PronunciationResult $pronunciationResultModel,
        ConvertSpeechToTextService $convertSpeechToText
    ) {
        $this->pronunciationResultModel = $pronunciationResultModel;
        $this->convertSpeechToText = $convertSpeechToText;
    }

    public function store($request)
    {
        $pronunciationDetail = PronunciationDetail::findOrFail($request['pronunciation_detail_id']);
        $data = $this->convertSpeechToText->convert($request);
        
        $calculatePoint = $this->calculatePoint($pronunciationDetail->content, $data['text']);
        
        $userId = auth(ConstantService::AUTH_USER)->user()->id;
        
        $checkResult = PronunciationResult::where('user_id',  $userId)->where('pronunciation_detail_id', $request['pronunciation_detail_id'])->first();
        if($checkResult) 
        {
            $checkResult->update([
                'content' => $data['text'],
                'point' => $calculatePoint['score'],
                'audio' => $data['url'],
            ]);
        }
        else{
            PronunciationResult::create( [
                'user_id' => $userId,
                'pronunciation_detail_id' => $request['pronunciation_detail_id'],
                'content' => $data['text'],
                'point' => $calculatePoint['score'],
                'audio' => $data['url'],
            ]);
        }

        return $this->sendSuccessResponse([
            'url' => $data['url'],
            'content' => $pronunciationDetail['content'],
            'answer' => $data['text'],
            'score' => $calculatePoint['score'],
            'result' => $calculatePoint['result'],
        ]);
    }

    public function calculatePoint($question, $answer)
    {

        $new_question = preg_replace('/[^a-zA-Z0-9\s]/', '', $question);
        $new_answer = preg_replace('/[^a-zA-Z0-9\s]/', '', $answer);

        $array_word_question = explode(' ', $new_question);
        $array_word_answer = explode(' ', $new_answer);

        $arrayResult = [];
        $score = 0;

        if (count($array_word_question) == 1) {
            $total_question_character = 0;
            $total_character_correct = 0;
            $array_character_question = str_split($new_question);
            $array_character_answer = str_split($new_answer);

            $array_character_mapping = [];
            foreach ($array_character_answer as $key => $character) {
                $array_character_mapping[$key]['answer'] = $character;
                $array_character_mapping[$key]['question'] = null;
            }

            foreach ($array_character_question as $key => $character) {
                $array_character_mapping[$key]['question'] = $character;
            }

            foreach ($array_character_mapping as $key => $character) {
                if (strtoupper($character['question']) == strtoupper($character['answer'])) {
                    $arrayResult[] = [
                        'word' => $character['answer'],
                        'is_correct' => true,
                    ];

                    $total_character_correct++;
                } else {
                    $arrayResult[] = [
                        'word' => $character['answer'],
                        'is_correct' => false,
                    ];
                }

                if (count($array_character_question)) {
                    $score = round($total_character_correct / count($array_character_question) * 100);
                }
            }
        } else {
            $array_word_mapping = [];
            $total_question_word = 0;
            $arrayWordCorrect = 0;
            foreach ($array_word_answer as $key => $word) {
                $array_word_mapping[$key]['answer'] = $word;
                $array_word_mapping[$key]['question'] = null;
            }

            foreach ($array_word_question as $key => $word) {
                $array_word_mapping[$key]['question'] = $word;
            }

            foreach ($array_word_mapping as $key => $word) {
                $total_question_word += strlen($word['question']);

                if (strtoupper($word['answer']) == strtoupper($word['question'])) {
                    $arrayResult[] = [
                        "word" => $word['answer'],
                        "is_correct" => true,
                    ];

                    $arrayWordCorrect += strlen($word['question']);
                } else {
                    $arrayResult[] = [
                        "word" => $word['answer'],
                        "is_correct" => false,
                    ];
                }
            }

            if ($total_question_word > 0) {
                $score = round($arrayWordCorrect / $total_question_word * 100);
            }
        }


        return [
            'result' => $arrayResult,
            'score' => $score,
        ];
    }
}