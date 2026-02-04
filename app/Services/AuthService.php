<?php

namespace App\Services;

use App\Http\Requests\Auth\LoginFormRequest;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected AuthRepository $authRepository
    ) {}

    public function authenticate(LoginFormRequest $request): RedirectResponse
    {
        $user = $this->attemptLogin(
            $request->only(['email', 'password'])
        );

        if (! $user) {
            return redirect()->back()->withErrors([
                'message' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function attemptLogin(array $credentials): ?User
    {
        $user = $this->authRepository->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        return $user;
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
