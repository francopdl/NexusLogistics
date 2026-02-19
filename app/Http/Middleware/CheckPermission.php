<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    // Procesar solicitud entrante
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        $userPermissions = $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->toArray();

        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                abort(403, 'No tienes permiso para realizar esta acción');
            }
        }

        return $next($request);
    }
}
