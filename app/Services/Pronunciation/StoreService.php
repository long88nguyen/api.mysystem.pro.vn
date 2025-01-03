<?php

namespace App\Services\Pronunciation;

use App\Models\Message;
use App\Models\Pronunciation;
use App\Models\PronunciationDetail;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreService extends BaseService
{
    protected $pronunciationModel;

    public function __construct(Pronunciation $pronunciationModel)
    {
        $this->pronunciationModel = $pronunciationModel;
    }

    public function store($request)
    {
        $data =  $request->all();
        $pronunciationSave = $this->pronunciationModel->create([
            'topic_name' => $data['topic_name'] ?? null,
            'user_id' => auth(ConstantService::AUTH_USER)->user()->id,
        ]);

        $arraySave = [];
        foreach($data['pronunciation_details'] as $key => $item)
        {   
            $saveItem = [
                'pronunciation_id' => $pronunciationSave['id'],
                'content' => $item['content'] ? strtolower($this->trimSpecialCharacters($item['content'])) : null,
                'ipa' => $item['ipa'] ?? null,
                'created_by' => auth(ConstantService::AUTH_USER)->user()->id,
            ];

            if(isset($item['audio']) && !empty($item['audio']))
            {
                $file = $item['audio'];
                $path = 'audio' . date("/Y/m/d/");
                $extension  = $file->getClientOriginalExtension();
                $image_name = time(). $key .  '.' . $extension;
                $filePath  = $path   . $image_name; // đặt file ảnh trong folder chính / năm / tháng / ngày upload

                Storage::disk('public')->putFileAs($path, $file, $image_name);// upload lên S3

                // Lấy URL đầy đủ của tệp trên S3
                $fileUrl = Storage::disk('public')->url($filePath);

                $saveItem['audio'] = $fileUrl;
            }

            $arraySave[] = $saveItem;
        }

        PronunciationDetail::insert($arraySave);
        
        return $this->sendSuccessResponse([]);
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