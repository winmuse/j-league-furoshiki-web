<?php

namespace App\Http\Middleware;

use Closure;

class ForceHttpProtocol
{
    public function handle($request, Closure $next)
    {
        if (app()->environment() !== 'local' && $request->method() == 'GET') {
            if (! empty($_SERVER["HTTP_X_FORWARDED_PROTO"])) {
                if ($_SERVER["HTTP_X_FORWARDED_PROTO"] !== 'https') {
                    return redirect()->secure($request->getRequestUri());
                }
            }
        }

        return $next($request);
    }
}
