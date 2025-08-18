<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Services\LdapAuthService;
use App\Models\User;

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
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(RouteServiceProvider::HOME);
    // }

    public function store(Request $request, LdapAuthService $ldap): RedirectResponse
    {
        // Form kamu memakai name="id_user" dan "password"
        $validated = $request->validate([
            'id_user'  => 'required|string',
            'password' => 'required|string',
            // 'remember' => 'nullable|boolean'
        ]);

        $login    = strtolower(trim($validated['id_user']));
        $password = $validated['password'];
        $remember = (bool) $request->boolean('remember');

        // Jika email @sdn.id → LDAP
        if (Str::contains($login, '@') && Str::endsWith($login, '@sdn.id')) {
            $ldapUser = $ldap->attempt($login, $password);

            if (!$ldapUser) {
                return back()
                    ->withInput($request->only('id_user'))
                    ->with('error', 'Email atau password LDAP salah.');
            }

            // Sinkronkan/buat user lokal agar bisa pakai sesi guard web
            $user = User::firstOrCreate(
                ['email' => $login],
                [
                    'name'     => $ldapUser['cn'] ?? strstr($login, '@', true),
                    'username' => strstr($login, '@', true),            // "joko" dari "joko@sdn.id"
                    'password' => bcrypt(Str::random(32)),               // dummy; tidak dipakai
                ]
            );

            Auth::login($user, $remember);
            $request->session()->regenerate();
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Selain @sdn.id → login lokal (uji email lalu username)
        if (
            Auth::attempt(['email' => $login, 'password' => $password], $remember) ||
            Auth::attempt(['username' => $login, 'password' => $password], $remember)
        ) {
            $request->session()->regenerate();
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()
            ->withInput($request->only('id_user'))
            ->with('error', 'Kredensial tidak valid.');
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
