<?php

namespace App\Services\Message;

use App\Events\MessagePosted;
use App\Models\Message;
use App\Services\_Abstract\BaseService;
use App\Services\_Constant\ConstantService;
use Illuminate\Support\Facades\Auth;
class MessageStoreService extends BaseService
{
    protected $messageModel;

    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    public function store($request)
    {
        $userId = auth(ConstantService::AUTH_USER)->user()->id;
        $message = new Message();
        $message->message = $request['message'];
        $message->user_id = $userId;
        $message->save();

        // $data = $this->messageModel->create([
        //     'message' => $request['message'],
        //     'user_id' => $request['user_id'],
        //     'created_at' => $request['created_at'],
        // ]);
        broadcast(new MessagePosted($message,  auth(ConstantService::AUTH_USER)->user()))->toOthers();
        return ['message' => $message->load('user')];
        // return $this->sendSuccessResponse($message);
    }
}