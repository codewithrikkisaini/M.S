<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

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
            if ($user->hasRole('housekeeping')) {
                return redirect()->route('housekeeping.index');
            }
            if ($user->hasRole('maintenance')) {
                return redirect()->route('maintenance.index');
            }
            return redirect()->route('dashboard');
        }

        $hotelName = \App\Models\Setting::where('key', 'hotel_name')->value('value') ?? 'Lodgiko PMS';
        return view('auth.login', compact('hotelName'));
    }

 public function login(Request $request)
{
    // Validate login fields + reCAPTCHA
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
        'g-recaptcha-response' => ['required'],
    ], [
        'g-recaptcha-response.required' => 'Please complete the reCAPTCHA.',
    ]);

    // Verify reCAPTCHA with Google
    $recaptchaResponse = Http::asForm()->post(
        'https://www.google.com/recaptcha/api/siteverify',
        [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]
    );

    if (!$recaptchaResponse->json('success')) {
        return back()
            ->withErrors([
                'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.',
            ])
            ->onlyInput('email');
    }

    // Remove reCAPTCHA from credentials
    unset($credentials['g-recaptcha-response']);

    // Attempt login
    if (Auth::attempt($credentials)) {

        $user = Auth::user();

        // Check hotel status
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

            if ($user->hasRole('receptionist')) {
                return redirect()->route('receptionist.dashboard')->with('status', 'Welcome back!');
            }

            if ($user->hasRole('housekeeping')) {
                return redirect()->route('housekeeping.index')->with('status', 'Welcome back!');
            }

            if ($user->hasRole('maintenance')) {
                return redirect()->route('maintenance.index')->with('status', 'Welcome back!');
            }
        }

        // Check user account status
        if ($user->status !== 'active') {

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account is currently inactive or pending approval.',
            ])->onlyInput('email');
        }

        // Regenerate session after successful login
        $request->session()->regenerate();

        // Super Admin
        if ($user->role?->slug === 'superadmin') {

            return redirect()
                ->route('superadmin.dashboard')
                ->with('status', 'Welcome back, Super Admin!');
        }

        // Receptionist
        if ($user->hasRole('receptionist')) {

            return redirect()
                ->intended(route('receptionist.dashboard'))
                ->with('status', 'Welcome back!');
        }

        // Other users
        return redirect()
            ->intended(route('dashboard'))
            ->with('status', 'Logged in successfully!');
    }

    // Invalid credentials
    return back()
        ->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])
        ->onlyInput('email');
}



    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
