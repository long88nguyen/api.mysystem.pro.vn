<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Message\MessageGetAllService;
use App\Services\Message\MessageStoreService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $messageGetAllService;
    protected $messageStoreService;

    public function __construct(MessageGetAllService $messageGetAllService, MessageStoreService $messageStoreService)
    {
        $this->messageGetAllService = $messageGetAllService;
        $this->messageStoreService = $messageStoreService;
    }

    public function getAll()
    {
        return $this->messageGetAllService->getAll();
    }

    public function store(Request $request)
    {
        return $this->messageStoreService->store($request);
    }
}
