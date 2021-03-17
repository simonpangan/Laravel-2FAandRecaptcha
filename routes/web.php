<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/recapchatryindex', function () {
    return view('recaptcha');
});

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\Captcha;

Route::post('/recapchatry', function (Request $request) {

    $request->validate([
        'name' => 'required|string|max:191',
        'email' => 'required|string|email|max:191|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'g-recaptcha-response' => new Captcha()
    ]);

    User::create([
        'name' => $request['name'],
        'email' => $request['email'],
        'password' => Hash::make($request['password']),
    ]);
})->name('recaptcha.store');

Auth::routes(['verify' => true]);

Route::middleware('auth','verified')->group(function () {

    Route::get('/twofactor', function() {
        return view('twofactor');
    })->name('twofactor.index');
    Route::get('verify/resend', [App\Http\Controllers\Auth\TwoFactorController::class, 'resend'])->name('verify.resend');
    Route::post('verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'store'])->name('verify.store');

    Route::middleware('twofactor')->group(function () {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
        ->name('home');
    });
});


 

