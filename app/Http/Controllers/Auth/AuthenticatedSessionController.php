<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
        // Autenticar utilizador

        $request->authenticate();

        // Regenerar sessão por segurança
        $request->session()->regenerate();

        // Utilizador autenticado
        $user = $request->user();

        // Obter os tenants aos quais o utilizador pertence
        $tenant = $user->tenants()->first();

        // O utilizador não pertence a nenhuma organização
        if (!$tenant) {

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'A sua conta não está associada a nenhuma organização.',
                ]);
        }

        // Definir tenant atual na sessão
        $request->session()->put(
            'tenant_id',
            $tenant->id
        );

        // Redirecionar para o dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->forget('tenant_id');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
