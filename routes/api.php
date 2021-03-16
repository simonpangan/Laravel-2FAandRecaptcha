<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/user', function (Request $request) {
    return response()->json(['name' => 'Behrang No']);
});


use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VerificationController;

Route::group([
    'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::delete('deleteacc', [AuthController::class, 'deleteacc']);
    
        Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name
        Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
        Route::get('verified', [AuthController::class, 'isVerified'])->middleware('verified');
    });
});

use App\Http\Controllers\API\ForgotPasswordController;

Route::post('forgot', [ResetPasswordController::class, 'forgot']);

use App\Http\Controllers\API\ResetPasswordController;
Route::post('password/reset/{token}', [ResetPasswordController::class, 'reset']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);