<?php

namespace App\Http\Controllers;

use App\Services\Pronunciation\GetAllService;
use App\Services\Pronunciation\GetByIdService;
use App\Services\Pronunciation\StoreService;
use Illuminate\Http\Request;

class PronunciationController extends Controller
{
    protected $storeService;
    protected $getAllService;
    protected $getByIdService;
    public function __construct(StoreService $storeService,
    GetAllService $getAllService,
    GetByIdService $getByIdService  // Add this line to inject GetByIdService instance into the controller constructor.
    )
    {
        $this->storeService = $storeService;
        $this->getAllService = $getAllService;
        $this->getByIdService = $getByIdService;
    }

    public function index()
    {
        return $this->getAllService->getAll();
    }

    public function store(Request $request) 
    {
        return $this->storeService->store($request);
    }

    public function getById($id)
    {
        return $this->getByIdService->getById($id);
    }
}
