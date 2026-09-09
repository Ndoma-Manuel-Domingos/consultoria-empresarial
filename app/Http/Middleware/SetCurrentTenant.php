<?php

namespace App\Http\Middleware;

use App\Support\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTenant
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
         * O utilizador precisa estar autenticado.
         */
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }


        /*
         * Tenant selecionado anteriormente.
         */
        $tenantId = session('tenant_id');


        /*
         * Se existe tenant na sessão,
         * verificamos se o utilizador realmente
         * pertence a esse tenant.
         */
        if ($tenantId) {

            $tenant = $user->tenants()
                ->where('tenants.id', $tenantId)
                ->first();

        } else {

            /*
             * Primeira entrada.
             *
             * Selecionamos o primeiro tenant
             * do utilizador.
             */
            $tenant = $user->tenants()->first();
        }


        /*
         * O utilizador não possui nenhuma organização.
         */
        if (!$tenant) {

            abort(
                403,
                'O utilizador não possui nenhuma organização.'
            );
        }


        /*
         * Guardar tenant na sessão.
         */
        session([
            'tenant_id' => $tenant->id,
        ]);


        /*
         * Definir tenant atual.
         */
        app(TenantManager::class)->set($tenant);


        /*
         * Continuar request.
         */
        return $next($request);
    }
}
