<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // Procesar solicitud entrante
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if (!auth()->user()->hasAnyRole($roles)) {
            abort(403, 'No tienes permiso para acceder a este recurso');
        }

        return $next($request);
    }
}
