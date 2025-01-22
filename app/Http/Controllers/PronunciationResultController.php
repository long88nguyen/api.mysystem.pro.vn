<?php

namespace App\Http\Controllers;

use App\Services\PronunciationResult\StoreService;
use Illuminate\Http\Request;

class PronunciationResultController extends Controller
{
    protected $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function store(Request $request)
    {
        return $this->storeService->test($request);
    }
}
