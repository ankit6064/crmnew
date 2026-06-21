<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        // Check if the user's account is active
        if (Auth::user()->is_active == 2) {
            // Log the user out immediately
            Auth::logout();

            // Redirect back with an error message
            return back()->withErrors(['email' => 'Your account is inactive. Please contact admin.']);
        }
        $request->session()->regenerate();
        if (Auth::user()->is_admin == 1) {
            return redirect()->intended(RouteServiceProvider::EmployeeHOME);
        } elseif (Auth::user()->is_admin == 2) {
            return redirect()->intended(RouteServiceProvider::ManagerHOME);
        } elseif (Auth::user()->is_admin == 3) {
            return redirect()->intended(route('home'));
        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Check if the email belongs to a submanager.
     */
    public function checkSubmanager(Request $request): \Illuminate\Http\JsonResponse
    {
        $email = $request->query('email');
        if (empty($email)) {
            return response()->json(['is_submanager' => false]);
        }

        $isSubmanager = \App\Models\User::where('email', $email)
            ->where('is_admin', 3) // SUBMANAGER
            ->where('is_active', 1)
            ->exists();

        return response()->json(['is_submanager' => $isSubmanager]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
