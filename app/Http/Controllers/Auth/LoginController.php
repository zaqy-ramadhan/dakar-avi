<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'npk' => 'required|string',
    //         'password' => 'required|min:6',
    //     ]);

    //     $user = \App\Models\User::where('npk', $request->npk)->first();

    //     if ($user && $user->password === $request->password) {
    //         Auth::login($user->with('dakarRole'));
    //         return redirect()->intended($this->redirectTo);
    //     }

    //     return back()->withErrors(['error' => 'npk atau password salah.']);
    // }

    protected function validateLogin(Request $request)
    {
        $validate = $request->validate([
            $this->username() => 'required|numeric',
            'password' => 'required|string',
        ]);
    }

    protected function credentials(Request $request)
    {
return $request->only($this->username(), 'password');
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
    protected $redirectTo = '/home';

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
