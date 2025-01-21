<?php

namespace App\Http\Controllers;

use App\Services\ChatRoom\DeleteService;
use App\Services\Pronunciation\GetAllService;
use App\Services\Pronunciation\GetByIdService;
use App\Services\Pronunciation\StoreService;
use App\Services\Pronunciation\UpdateService;
use Illuminate\Http\Request;

class PronunciationController extends Controller
{
    protected $storeService;
    protected $getAllService;
    protected $getByIdService;
    protected $updateService;
    protected $deleteService;

    public function __construct(StoreService $storeService,
    GetAllService $getAllService,
    GetByIdService $getByIdService,  // Add this line to inject GetByIdService instance into the controller constructor.
    UpdateService $updateService,
    DeleteService $deleteService
    )
    {
        $this->storeService = $storeService;
        $this->getAllService = $getAllService;
        $this->getByIdService = $getByIdService;
        $this->deleteService = $deleteService;
        $this->updateService = $updateService;
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

    public function update(Request $request, $id)
    {
        return $this->updateService->update($request, $id);
    }

    public function delete($id)
    {
        return $this->deleteService->delete($id);
    }
}
