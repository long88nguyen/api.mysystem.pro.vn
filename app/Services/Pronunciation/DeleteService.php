<?php

namespace App\Services\Pronunciation;

use App\Models\Message;
use App\Models\Pronunciation;
use App\Models\PronunciationDetail;
use App\Models\PronunciationResult;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeleteService extends BaseService
{
    protected $pronunciationModel;

    public function __construct(Pronunciation $pronunciationModel)
    {
        $this->pronunciationModel = $pronunciationModel;
    }

    public function delete($id)
    {
       $domain = env('APP_ENV') == 'local' ? 'http://localhost:8888/storage' : 'https://api.mysystem.pro.vn/storage';
       $pronunciationDetails = PronunciationDetail::where('pronunciation_id', $id)->get();
       foreach($pronunciationDetails as $detail)
       {
            if($detail->audio){

                $newFile = str_replace($domain, '', $detail->audio);
                Storage::disk('public')->delete($newFile);
            }

            $detail->delete();

            $pronunciationResults = PronunciationResult::where('pronunciation_detail_id', $detail->id)->get();
            foreach($pronunciationResults as $result)
            {
                if($result->audio){

                    $newFileResult = str_replace($domain, '', $result->audio);
                    Storage::disk('public')->delete($newFileResult);
                }
                $result->delete();
            }
       }

       $this->pronunciationModel->findOrFail($id)->delete();
       
       return $this->sendSuccessResponse([]);
    }
}