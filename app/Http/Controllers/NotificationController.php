<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Services\_Constant\ConstantService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getMessage() {
        $result = Notification::orderByDesc('id')->get();
        return response()->json([
            'data' => $result,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $userId = auth(ConstantService::AUTH_USER)->user()->id;
        $array = [
            'user_id' => $userId,
            'title' => 'default',
            'message' => $request['message'] ?? 'default',
        ];

        $result = Notification::create($array);
        
        event(new NotificationEvent($array));

        return response()->json([
            'status' => true,
        ]);
    }
}
