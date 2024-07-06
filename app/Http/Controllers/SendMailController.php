<?php

namespace App\Http\Controllers;

use App\Jobs\SendMail;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
    public function sendMail(Request $request)
    {   
        $details['email'] = 'long432hvt@gmail.com';
        $details['content'] = $request['content'];
        SendMail::dispatch($details);
        return response()->json(['status' => true, 'message' => 'Send Mail Successfully!']);
    }
}
