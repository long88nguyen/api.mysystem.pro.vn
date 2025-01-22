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

        $data = $this->convertSpeechToText->convertGoogleCloud($request);
        $questionContent = strtolower($this->trimSpecialCharacters($pronunciationDetail->content));
        $answerContent = strtolower($this->trimSpecialCharacters($data['text']));
        $calculateResult = $this->calculateResult($questionContent, $answerContent);
        $userId = auth(ConstantService::AUTH_USER)->user()->id;
        
        $checkResult = PronunciationResult::where('user_id',  $userId)->where('pronunciation_detail_id', $request['pronunciation_detail_id'])->first();
        if($checkResult) 
        {
            $checkResult->update([
                'content' => strtolower($this->trimSpecialCharacters($data['text'])),
                'point' => $calculateResult['accuracy'],
                'audio' => $data['url'],
                'result' => json_encode($calculateResult['result']),
            ]); 
        }
        else{
            PronunciationResult::create( [
                'user_id' => $userId,
                'pronunciation_detail_id' => $request['pronunciation_detail_id'],
                'content' => strtolower($this->trimSpecialCharacters($data['text'])),
                'point' => $calculateResult['accuracy'],
                'audio' => $data['url'],
                'result' => json_encode($calculateResult['result']),
            ]);
        }

        return $this->sendSuccessResponse([
            'url' => $data['url'],
            'text' => $data['text'],
        ]);
    }

    public function calculateResult($prompt, $answer)
    {
        // Kiểm tra prompt là từ hay câu
        if (strpos($prompt, ' ') === false) {
            // Là từ, tách thành mảng ký tự
            $promptItems = str_split($prompt);
            $answerItems = str_split($answer);
        } else {
            // Là câu, tách thành mảng từ
            $promptItems = explode(' ', $prompt);
            $answerItems = explode(' ', $answer);
        }

        $result = [];
        $correctCount = 0;

        // So sánh từng phần tử trong đề bài với kết quả
        foreach ($promptItems as $index => $item) {
            $isCorrect = isset($answerItems[$index]) && $item === $answerItems[$index];
            $result[] = [
                'text' => $item,
                'isCorrect' => $isCorrect,
            ];
            if ($isCorrect) {
                $correctCount++;
            }
        }

        // Tính tỷ lệ chính xác
        $accuracy = (count($promptItems) > 0) ? ($correctCount / count($promptItems)) * 100 : 0;

        return [
            'result' => $result,
            'accuracy' => round($accuracy) // Làm tròn đến 2 chữ số thập phân
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

    public function test($request)
    {
        $audioFile = $request->file('audio');
        $imageName = time() . '.' . 'mp3';
        $path = $audioFile->storeAs('public/audio', $imageName);
        $apiPath = Storage::disk('public')->url('audio/'.$imageName);
        
        return $this->sendSuccessResponse([
            'url' => $apiPath,
        ]);
    }
}
