<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\ArtificialIntelligenceController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PronunciationController;
use App\Http\Controllers\PronunciationResultController;
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


Route::post('/login', [AuthController::class, 'login']);
Route::post('/convert-speech-to-text', [ArtificialIntelligenceController::class, 'convert']);
Route::post('/convert-text-to-speech', [ArtificialIntelligenceController::class, 'convertTextToSpeech']);
Route::post('/chat-gpt-prompt', [ArtificialIntelligenceController::class, 'chatGPTPrompt']);

Route::get('/employee', [EmployeeController::class, 'index']);
Route::post('/employee/store', [EmployeeController::class, 'store']);
Route::delete('/employee/delete/{id}', [EmployeeController::class, 'delete']);

Route::group(['middleware' => ['auth:' . ConstantService::AUTH_USER]], function () {
    Route::prefix('message')->group(function() {
        Route::get('/', [MessageController::class, 'getAll']);
        Route::post('/', [MessageController::class, 'store']);
    });

    Route::prefix('chat-room')->group(function () {
        Route::get('/index', [ChatRoomController::class, 'index'])->name('chat-room.index');
        Route::post('/store', [ChatRoomController::class, 'store'])->name('chat-room.store');
        Route::get('/edit/{id}', [ChatRoomController::class, 'edit'])->name('chat-room.edit');
        Route::post('/update/{id}', [ChatRoomController::class, 'update'])->name('chat-room.update');
        Route::post('/delete/{id}', [ChatRoomController::class, 'delete'])->name('chat-room.delete');
        Route::get('/get-by-id/{id}', [ChatRoomController::class, 'getById'])->name('chat-room.get-by-id');
    });

    Route::prefix('chat-message')->group(function () {
        Route::get('/get-by-chat-room-id/{chat_room_id}', [ChatMessageController::class, 'getByChatRoomId']);
        Route::post('/store-text', [ChatMessageController::class, 'storeText']);
        Route::post('/store-speech', [ChatMessageController::class, 'storeSpeech']);
    });

    Route::prefix('pronunciation')->group(function () {
        Route::get('/index', [PronunciationController::class, 'index']);
        Route::post('/store', [PronunciationController::class, 'store']);
        Route::get('/get-by-id/{id}', [PronunciationController::class, 'getById']);
        Route::put('/update/{id}', [PronunciationController::class, 'update']);
        Route::delete('/delete/{id}', [PronunciationController::class, 'delete']);
    });


    Route::prefix('pronunciation-result')->group(function () {
        Route::post('/store', [PronunciationResultController::class, 'store']);
    });

    
    Route::post('/send-mail', [SendMailController::class, 'sendMail']);
});

