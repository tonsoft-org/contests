<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
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
        if ($request->ip() !== env('ADMIN_IP')) {
            return response(
                [
                    'errors' => [['message' => 'AUTH error']],
                    $request->ip(),
                    env('ADMIN_IP')
                ]
            );
        }

        return $next($request);
    }
}
