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
            if ($user->role?->slug === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            }
            if ($user->hasRole('receptionist')) {
                return redirect()->route('receptionist.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $hotelName = \App\Models\Setting::where('key', 'hotel_name')->value('value') ?? 'Lodgiko PMS';
        return view('auth.login', compact('hotelName'));
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
                        'email' => 'Your hotel is waiting for Super Admin approval. Please wait until your hotel is approved.',
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

            if ($user->role?->slug === 'superadmin') {
                return redirect()->route('superadmin.dashboard')->with('status', 'Welcome back, Super Admin!');
            }

            if ($user->hasRole('receptionist')) {
                return redirect()->intended(route('receptionist.dashboard'))->with('status', 'Welcome back!');
            }

            return redirect()->intended(route('dashboard'))->with('status', 'Logged in successfully!');
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
        return redirect()->route('login');
    }
}
