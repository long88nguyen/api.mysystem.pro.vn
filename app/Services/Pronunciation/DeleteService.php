<?php

namespace App\Services\Pronunciation;

use App\Models\Message;
use App\Models\Pronunciation;
use App\Models\PronunciationDetail;
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
        
    }
}