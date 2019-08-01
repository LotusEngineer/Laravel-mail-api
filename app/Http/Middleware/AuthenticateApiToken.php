<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class AuthenticateApiToken extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if ($request->hasHeader('Authorization')) {
            list ($bearer, $token) = explode(' ', $request->header('Authorization'));
            if ($token == config('auth.apitoken')) {
                return $next($request);
            }

        }
        return response()
            ->json([
                'error' => 'Not authorized'
            ], 403);
    }
}
