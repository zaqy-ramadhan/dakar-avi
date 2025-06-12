<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CryptoJS;


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

    public function showLoginForm()
    {
        $npk_key = env('NPK_ENCRYPTION_KEY');
        $pass_key = env('PASS_ENCRIPTION_KEY');
        return view('auth.login', compact('npk_key', 'pass_key'));
    }

    protected function validateLogin(Request $request)
    {
        $decryptedNpk = CryptoJS::decrypt(
            env('NPK_ENCRYPTION_KEY'),
            $request->input('npk')
        );

        $decryptedPassword = CryptoJS::decrypt(
            env('PASS_ENCRIPTION_KEY'),
            $request->input('password')
        );

        $request->merge([
            'npk' => $decryptedNpk,
            'password' => $decryptedPassword,
        ]);

        $request->validate([
            'npk' => 'required|numeric',
            'password' => 'required|string',
        ]);
    }



    protected function credentials(Request $request)
    {
        // dd($request);
        return [
            'npk' => $request->get('npk'),
            'password' => $request->get('password')
        ];
    }


    public function username()
    {
        return 'npk';
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
