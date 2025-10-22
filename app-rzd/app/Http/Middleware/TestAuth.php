<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('X-Test-User')) {
            $testUserLogin = $request->header('X-Test-User');
            $user = User::where('login', $testUserLogin)->first();

            if ($user) {
                Auth::login($user);
            } else {
                // Если нужен пользователь не по логину, а по ID
                $user = User::find((int)$testUserLogin);
                if ($user) {
                    Auth::login($user);
                }
            }
        }

        return $next($request);
    }
}
