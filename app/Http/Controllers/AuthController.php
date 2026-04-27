<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    /* ------------------------------------------------------------------ */
    /* LOGIN                                                             */
    /* ------------------------------------------------------------------ */

    public function showLogin(Request $request)
    {
        if ($request->has('source')) {
            session(['auth_source' => $request->source]);
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $source = session('auth_source', 'web');
            $redirectUrl = $source === 'mobile' ? route('mobile') : route('dashboard');
            return redirect()->intended($redirectUrl);
        }

        return back()
            ->withErrors(['email' => 'Identifiants incorrects.'])
            ->onlyInput('email');
    }

    /* ------------------------------------------------------------------ */
    /* REGISTER                                                          */
    /* ------------------------------------------------------------------ */

    public function showRegister(Request $request)
    {
        if ($request->has('source')) {
            session(['auth_source' => $request->source]);
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'firstname' => $request->firstname,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $source = session('auth_source', 'web');
        $redirectUrl = $source === 'mobile' ? route('mobile') : route('dashboard');

        return redirect()->intended($redirectUrl);
    }

    /* ------------------------------------------------------------------ */
    /* LOGOUT                                                            */
    /* ------------------------------------------------------------------ */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
