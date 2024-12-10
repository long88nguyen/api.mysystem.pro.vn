<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthAdminController extends Controller
{
    public function loginForm()
    {
        return view('auth-admin.login');
    }

    public function login(Request $request)
    {
        $md5Password = md5($request->password);
        $user = User::where('name', $request->name)
            ->where('password', $md5Password)
            ->first();
        if ($user) {
            Auth::login($user);
            return redirect('/telescope');
        } else {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }
    }
}
