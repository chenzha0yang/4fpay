<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\Check\CheckPermissionController;
use Closure;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = CheckPermissionController::checkPermission($request);

        if ($response !== true) {
            return $response;
        }

        return $next($request);
    }
}
