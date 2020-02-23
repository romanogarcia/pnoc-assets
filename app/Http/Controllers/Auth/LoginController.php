<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Utility;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxAttempts = 60; //60 times to attempt
        $this->decayMinutes = 1; //1 minute

        $this->middleware('guest')->except('logout');
    }

    public function username (){
        $loginType = request()->input('username');
        $this->username = filter_var($loginType, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$this->username => $loginType]);

        // dd(request());
        return property_exists($this, 'username') ? $this->username : 'email';
    }

    public function credentials(Request $request){
        $is_valid = [$this->username => $request->username, 'password'=>$request->password, 'is_locked'=>'0'];

        Utility::account_login_attempt($this->username, $request->username, $request->password);
        return $is_valid;
    }


    


}
