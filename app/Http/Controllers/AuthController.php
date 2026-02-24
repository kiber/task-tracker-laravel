<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return back()->withInput()->withErrors(['email' => 'These credentials do not match our records.']);
    }

    /**
     * @deprecated
     * @see login()
     */
    public function __login(LoginRequest $request)
    {
        $ipThrottleKey = 'login:' . $request->ip();
        $emailThrottleKey = strtolower($request->input('email'));

        if (RateLimiter::tooManyAttempts($ipThrottleKey, 100)) {
//            throw ValidationException::withMessages(['email' => 'Too many login attempts. Please try again in a few minutes.']);
            return back()->withErrors(['email' => 'Too many login attempts. Please try again in a few minutes.']);
        }

        if (RateLimiter::tooManyAttempts($emailThrottleKey, 5)) {
//            throw ValidationException::withMessages(['email' => 'Too many login attempts. Please try again in a few minutes.']);
            return back()->withErrors(['email' => 'Too many login attempts. Please try again in a few minutes.']);
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            RateLimiter::clear($ipThrottleKey);
            RateLimiter::clear($emailThrottleKey);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        RateLimiter::hit($ipThrottleKey);
        RateLimiter::hit($emailThrottleKey);

        return back()->withErrors(['email' => 'These credentials do not match our records.']);
    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
