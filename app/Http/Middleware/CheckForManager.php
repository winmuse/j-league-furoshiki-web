<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Spport\Facades\Auth;
use App\Models\Admin;

class CheckForManager
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * @param Request $request
     * @param Closure $next
     * @param string $guard
     * 
     * @return RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next, string $guard)
    {
        /** @var Admin $user */
        $user = auth()->user();

        if (strpos($guard, $user->role) === false) {
            return redirect()->route('admin.home');
        }

        return $next($request);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isExceptArray($request)
    {
        foreach ($this->except as $exceptRouteName) {
            $except = route($exceptRouteName);
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs("{$except}*") || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
