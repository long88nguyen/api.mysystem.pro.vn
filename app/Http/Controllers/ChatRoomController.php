<?php

namespace App\Http\Controllers;

use App\Services\ChatRoom\GetByIdService;
use App\Services\ChatRoom\StoreService;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    protected $storeService;
    protected $getByIdService;

    public function __construct(StoreService $storeService, GetByIdService $getByIdService) 
    {
        $this->storeService = $storeService;
        $this->getByIdService = $getByIdService;
    }

    public function index()
    {

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
