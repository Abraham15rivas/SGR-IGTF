<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAnalyst
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = \Auth::user();
        if ($user->role_id == 1 || $user->role_id == 3) {
            return $next($request);
        } else {
            abort(403, 'No tienes permisos para continuar.');
        }
    }
}
