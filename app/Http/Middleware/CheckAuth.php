<?php

namespace App\Http\Middleware;

use App\Traits\Response as TraitsResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    use TraitsResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/*')) {
            if (auth("api")->check()) {
                return $next($request);
            } else {
                return $this->unauthorize();
            }
        }
    }
}
