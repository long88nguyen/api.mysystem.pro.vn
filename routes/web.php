<?php

use App\Http\Controllers\AuthAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthAdminController::class, 'loginForm'])->name('auth-login.login-form');
Route::post('/login', [AuthAdminController::class, 'login'])->name('auth-login.login');
