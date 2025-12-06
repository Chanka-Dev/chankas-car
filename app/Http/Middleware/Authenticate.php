<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Agregar mensaje flash cuando la sesión ha expirado
            session()->flash('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
            return route('login');
        }
        
        return null;
    }
}
