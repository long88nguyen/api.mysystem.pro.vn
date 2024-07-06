<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SendMailController;
use App\Services\_Constant\ConstantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/employee', [EmployeeController::class, 'index']);
Route::post('/employee/store', [EmployeeController::class, 'store']);
Route::delete('/employee/delete/{id}', [EmployeeController::class, 'delete']);

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:' . ConstantService::AUTH_USER]], function () {
    Route::prefix('message')->group(function() {
        Route::get('/', [MessageController::class, 'getAll']);
        Route::post('/', [MessageController::class, 'store']);
    });

    Route::post('/send-mail', [SendMailController::class, 'sendMail']);
});
