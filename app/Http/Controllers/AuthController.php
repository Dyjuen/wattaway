<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('account')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (session()->has('provisioning_token')) {
                $token = session('provisioning_token');
                session()->forget('provisioning_token');
                return redirect()->route('pairing.confirm', ['token' => $token]);
            }

            $account = Auth::guard('account')->user();
            if ($account->isAdmin()) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:accounts',
            'email' => 'required|string|email|max:255|unique:accounts',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $account = Account::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('account')->login($account);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('account')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
