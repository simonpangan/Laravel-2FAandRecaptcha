<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\ResetsPasswords; 

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    public function reset(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'token' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255' ],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);


        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }
    
    protected function resetPassword($user, $password) 
    { 
        $this->setUserPassword($user, $password); 
        //Here Larvel tries to set the "Remember me" cookie 
        //$user->setRememberToken(Str::random(60)); 

        $user->save(); 
        event(new PasswordReset($user)); 
        //By default, Laravel will attempt to automatically log in the user 
        //$this->guard()->login($user); 
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json(['success' => ["message" => trans($response)] ], 200);                          
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        // return redirect()->back()
        //             ->withInput($request->only('email'))
        //             ->withErrors(['email' => trans($response)]);
        return response()->json(['error' => ["message" => trans($response)] ], 422);            
    }

}
