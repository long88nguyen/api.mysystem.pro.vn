<?php

namespace App\Http\Controllers;

use App\Jobs\SendMail;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
    public function sendMail(Request $request)
    {   
        $details['email'] = $request['email'];
        $details['content'] = '<h1>Test Mail</h1>';
        SendMail::dispatch($details);
    }
}
