<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->get('email'))->first();

        $update = $user->update([
                'password_hash' => bcrypt($request->password),
                'password' => bcrypt($request->password),
        ]);
                    
        $log = ActivityLog::create([
            'actor_id' => $user->id,
            'employee_id' => $user->id,
            'note' => 'Updating Password',
            'table_name' => 'users',
            'table_id' => $user->id,
        ]);

        // dd($response);

        return $update
            //? redirect($this->redirectPath())->with('status', __($response))
            ? redirect('/login')->with('status', 'Password berhasil direset, silakan login kembali.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($update)]);
    }

    /**
     * Get the password reset credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }
}
