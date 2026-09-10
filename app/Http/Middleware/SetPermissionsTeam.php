<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionsTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = session('tenant_id');

        if ($tenantId) {
            setPermissionsTeamId($tenantId);
        }

        return $next($request);
    }
}
