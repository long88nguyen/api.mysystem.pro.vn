<?php

namespace App\Services\Pronunciation;

use App\Models\Message;
use App\Models\Pronunciation;
use App\Models\PronunciationDetail;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GetByIdService extends BaseService
{
    protected $pronunciationModel;

    public function __construct(Pronunciation $pronunciationModel)
    {
        $this->pronunciationModel = $pronunciationModel;
    }

    public function getById($id)
    {
        $userId = auth(ConstantService::AUTH_USER)->user()->id;
        $result = $this->pronunciationModel->with(['pronunciation_details', 'pronunciation_details.pronunciation_result' => function ($query) use($userId){
            $query->where('user_id', $userId);
        }])->findOrFail($id);
        return $this->sendSuccessResponse($result);
    }
}