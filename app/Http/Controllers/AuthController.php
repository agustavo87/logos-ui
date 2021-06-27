<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Arete\Sofrosine\Services\Locale;

class AuthController extends Controller
{

    protected Locale $locale;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Show login page
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function show(Request $request, $lang)
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function login(Request $request, $lang)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $remember = $request->has('remember') ? true : false;

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect(route('home', [ 'locale' => $this->locale->getLocale()]));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle an logout.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function logout(Request $request, $lang)
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
