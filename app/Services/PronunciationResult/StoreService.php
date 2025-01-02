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
        if($data && $data['text'])
        {
            $calculatePoint = $this->calculatePoint($pronunciationDetail->content, strtolower($data['text']));
        }
        dd($data);
        
        $userId = auth(ConstantService::AUTH_USER)->user()->id;
        
        $checkResult = PronunciationResult::where('user_id',  $userId)->where('pronunciation_detail_id', $request['pronunciation_detail_id'])->first();
        if($checkResult) 
        {
            $checkResult->update([
                'content' => strtolower($this->trimSpecialCharacters($data['text'])),
                'point' => $calculatePoint['score'],
                'audio' => $data['url'],
                'result' => json_encode($calculatePoint['result']),
            ]); 
        }
        else{
            PronunciationResult::create( [
                'user_id' => $userId,
                'pronunciation_detail_id' => $request['pronunciation_detail_id'],
                'content' => strtolower($this->trimSpecialCharacters($data['text'])),
                'point' => $calculatePoint['score'],
                'audio' => $data['url'],
                'result' => json_encode($calculatePoint['result']),
            ]);
        }

        return $this->sendSuccessResponse([
            'url' => $data['url'],
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
                if (isset($character['answer']) && strtolower($character['question']) == strtolower($character['answer'])) {
                    $arrayResult[] = [
                        'word' => $character['answer'],
                        'is_correct' => true,
                    ];

                    $total_character_correct++;
                } else {
                    $arrayResult[] = [
                        'word' => ' ',
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

                if ( isset($word['answer']) && strtoupper($word['answer']) == strtoupper($word['question'])) {
                    $arrayResult[] = [
                        "word" => $word['answer'],
                        "is_correct" => true,
                    ];

                    $arrayWordCorrect += strlen($word['question']);
                } else {
                    $arrayResult[] = [
                        "word" => ' ',
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

    public static function trimSpecialCharacters($string)
    {   
        // Thay thế các loại dấu phẩy khác nhau bằng dấu phẩy chuẩn ","
        $string = preg_replace("/[‚，、﹐﹑]/u", ",", $string);
        $string = preg_replace("/[‘’]/u", "'", $string); // thay thế dấu ‘ hoặc dấu ’ bằng dấu '
        $result = preg_replace('/^[^a-zA-Z0-9]+|[^a-zA-Z0-9]+$/', '', $string); // loại bỏ cả ký tự đặc biệt ở 2 đầu
        return trim($result); // loại bỏ khoảng trắng ở 2 đầu
    }
}