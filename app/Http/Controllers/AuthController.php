<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('superadmin')) {
                return redirect()->route('superadmin.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->hotel_id) {
                $hotel = \App\Models\Hotel::find($user->hotel_id);
                if ($hotel && $hotel->status === 'pending') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->withErrors([
                        'email' => '⏳ Your hotel registration ("' . $hotel->name . '") is currently pending approval by Super Admin. Access will open once approved!',
                    ])->onlyInput('email');
                } elseif ($hotel && $hotel->status === 'rejected') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->withErrors([
                        'email' => '❌ Your hotel registration ("' . $hotel->name . '") was rejected. Please contact support.',
                    ])->onlyInput('email');
                }
            }

            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Your account is currently inactive or pending approval.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            if ($user->hasRole('superadmin')) {
                return redirect()->route('superadmin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
