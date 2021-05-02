<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\UserLoginLog;
use Jenssegers\Agent\Agent;
use App\Helpers\Helper;

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
    protected $redirectTo   = RouteServiceProvider::HOME;

    protected $maxAttempts  = 5; // Default is 5
    protected $decayMinutes = 2; // Default is 1

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'email'; //or whatever field
    }

    public function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
                   
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $time = Carbon::now()->toDateTimeString();
        $ip = Helper::getUserIpAddr();

        $user->update([
            'last_login_at' => $time,
            'last_login_ip' => $ip,
        ]);

        \App\Helpers\Helper::userLoginLog($user->id);
    }

}
