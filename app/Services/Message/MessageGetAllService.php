<?php

namespace App\Services\Message;

use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Illuminate\Support\Facades\Auth;
class MessageGetAllService extends BaseService
{
    protected $messageModel;

    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    public function getAll()
    {
        $data = $this->messageModel->with('user')->orderBy('id', 'asc')->get();
        return $this->sendSuccessResponse($data);
    }
}