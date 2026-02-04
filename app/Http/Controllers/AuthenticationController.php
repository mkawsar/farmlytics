<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginFormRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticationController extends Controller
{
    public function login(): Response
    {
        return Inertia::render('auth/Login');
    }

    public function authenticate(LoginFormRequest $request, AuthService $authService): RedirectResponse
    {
        return $authService->authenticate($request);
    }

    public function logout(Request $request, AuthService $authService): RedirectResponse
    {
        return $authService->logout($request);
    }
}
