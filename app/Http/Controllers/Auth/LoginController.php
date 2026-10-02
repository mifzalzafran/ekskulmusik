<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the application login form.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');

        // Look up user including soft-deleted records to prevent user enumeration
        $user = User::withTrashed()->where('email', $credentials['email'])->first();

        // 1. Verify credentials: if user does not exist, run a dummy Hash::check to mitigate timing differences
        if (! $user) {
            Hash::check($credentials['password'], '$2y$12$e8wV.z6q4lH9oEknbF8Ose6k67YxI9vjXgq0yZ1W7oA6G.XfS6bma');
            RateLimiter::hit($request->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($request->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 2. Verified credentials: check if soft-deleted
        if ($user->trashed()) {
            RateLimiter::hit($request->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => 'Your account has been deleted. Please contact the administrator.',
            ]);
        }

        // 3. Verified credentials: check if account is inactive
        if (! $user->is_active) {
            RateLimiter::hit($request->throttleKey(), 60);

            throw ValidationException::withMessages([
                'email' => 'Your account is currently inactive. Please contact the administrator.',
            ]);
        }

        // Credentials valid, active, and not deleted: log in
        $request->clearRateLimiter();

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Always redirect strictly by role; do NOT use redirect()->intended()
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
