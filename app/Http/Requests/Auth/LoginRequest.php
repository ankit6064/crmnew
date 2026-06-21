<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $role = $this->input('role');

        if ($role && in_array($role, [1, 3])) {
            // Attempt to authenticate using the selected role
            $credentials['is_admin'] = $role;
            if (Auth::attempt($credentials, $this->boolean('remember'))) {
                RateLimiter::clear($this->throttleKey());
                session(['login_role' => $role]);
                request()->session()->flash('success', 'You have successfully logged in!');
                return;
            }

            // Fallback: If they wanted Employee (1) but only Submanager (3) exists in DB
            if ($role == 1) {
                $credentials['is_admin'] = 3;
                if (Auth::attempt($credentials, $this->boolean('remember'))) {
                    RateLimiter::clear($this->throttleKey());
                    session(['login_role' => 1]); // Set session role to employee (1)
                    request()->session()->flash('success', 'You have successfully logged in!');
                    return;
                }
            }
        } else {
            // Standard login path
            if (Auth::attempt($credentials, $this->boolean('remember'))) {
                RateLimiter::clear($this->throttleKey());
                // Save default role to session
                session(['login_role' => Auth::user()->is_admin]);
                request()->session()->flash('success', 'You have successfully logged in!');
                return;
            }
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
