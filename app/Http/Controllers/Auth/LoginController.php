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
        $public_key = env('PUBLIC_KEY');
        
        // Regenerate CSRF token untuk mencegah token expired
        session()->regenerateToken();
        
        return view('auth.login', compact('npk_key', 'pass_key', 'public_key'));
    }

    protected function validateLogin(Request $request)
    {
        $npkEncrypted = base64_decode($request->npk);
        $passwordEncrypted = base64_decode($request->password);
        $privateKey = env('PRIVATE_KEY');

        $npkDecrypted = '';
        $passwordDecrypted = '';

        if (!openssl_private_decrypt($npkEncrypted, $npkDecrypted, $privateKey)) {
            abort(400, "Unable to decrypt NPK");
        }

        if (!openssl_private_decrypt($passwordEncrypted, $passwordDecrypted, $privateKey)) {
            abort(400, "Unable to decrypt password");
        }

        $request->merge([
            'npk' => $npkDecrypted,
            'password' => $passwordDecrypted,
        ]);

        // dd($request);

        $request->validate([
            'npk' => 'required|string',
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
     * Attempt to log the user into the application.
     * Override to disable remember_token.
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            false  // remember = false, tidak update remember_token
        );
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
