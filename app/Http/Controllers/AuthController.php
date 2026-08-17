<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private function generateCaptcha(): array
    {
        $first = random_int(2, 9);
        $second = random_int(2, 9);

        return [
            'question' => $first . ' + ' . $second,
            'answer' => (string) ($first + $second),
        ];
    }

    private function refreshCaptcha(): array
    {
        $captcha = $this->generateCaptcha();
        session(['login_captcha' => $captcha]);

        return $captcha;
    }

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
            if ($user->hasRole('housekeeping')) {
                return redirect()->route('housekeeping.index');
            }
            if ($user->hasRole('maintenance')) {
                return redirect()->route('maintenance.index');
            }
            return redirect()->route('dashboard');
        }

        $hotelName = \App\Models\Setting::where('key', 'hotel_name')->value('value') ?? 'Lodgiko PMS';
        $captcha = session('login_captcha') ?? $this->refreshCaptcha();

        return view('auth.login', compact('hotelName', 'captcha'));
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
                'captcha' => ['required', 'numeric', function ($attribute, $value, $fail) {
                    $expected = session('login_captcha.answer');

                    if ($expected === null || (string) $value !== (string) $expected) {
                        $fail('CAPTCHA answer is incorrect.');
                    }
                }],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->refreshCaptcha();
            throw $e;
        }

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->hotel_id) {
                $hotel = \App\Models\Hotel::find($user->hotel_id);
                if ($hotel && $hotel->status === 'pending') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    $this->refreshCaptcha();
                    return back()->withErrors([
                        'email' => 'Your hotel is waiting for Super Admin approval. Please wait until your hotel is approved.',
                    ])->onlyInput('email');
                } elseif ($hotel && $hotel->status === 'rejected') {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    $this->refreshCaptcha();
                    return back()->withErrors([
                        'email' => 'Your hotel registration ("' . $hotel->name . '") was rejected. Please contact support.',
                    ])->onlyInput('email');
                }
            }

            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $this->refreshCaptcha();
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

            if ($user->hasRole('housekeeping')) {
                return redirect()->intended(route('housekeeping.index'))->with('status', 'Welcome back!');
            }

            if ($user->hasRole('maintenance')) {
                return redirect()->intended(route('maintenance.index'))->with('status', 'Welcome back!');
            }

            return redirect()->intended(route('dashboard'))->with('status', 'Logged in successfully!');
        }

        $this->refreshCaptcha();

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
