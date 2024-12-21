<?php

namespace App\Http\Controllers;

use App\Services\ChatRoom\GetAllService;
use App\Services\ChatRoom\GetByIdService;
use App\Services\ChatRoom\StoreService;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    protected $storeService;
    protected $getByIdService;
    protected $getAllService;

    public function __construct(StoreService $storeService, GetByIdService $getByIdService, GetAllService $getAllService) 
    {
        $this->storeService = $storeService;
        $this->getByIdService = $getByIdService;
        $this->getAllService = $getAllService;
    }

    public function index(Request $request)
    {
        return $this->getAllService->getAll($request);
    }

    public function getById($id){
        return $this->getByIdService->getById($id);
    }

    public function store(Request $request)
    {
        return $this->storeService->store($request);
    }

    public function delete($id)
    {
        
    }
}
