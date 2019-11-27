<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\Check\CheckLoginController;
use Closure;

class CheckLogin
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
        $response = CheckLoginController::checkLogin($request);

        if ($response !== true) {
            return $response;
        }

        return $next($request);
    }
}
