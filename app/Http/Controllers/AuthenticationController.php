<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticationController extends Controller
{
    public function login(): RedirectResponse|Response
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return Inertia::render('auth/login');
    }
}
