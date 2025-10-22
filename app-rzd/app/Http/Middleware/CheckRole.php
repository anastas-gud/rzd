<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check() || Auth::user()->role->title !== strtoupper($role)) {
            abort(403, 'Нет доступа');
        }
        return $next($request);
    }
}
