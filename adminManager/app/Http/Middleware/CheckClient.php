<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\Check\CheckClientController;
use Closure;

class CheckClient
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

        $response = CheckClientController::checkClient($request);
        if ($response !== true) {
            return $response;
        }
        return $next($request);
    }
}
