<?php

namespace App\Http\Middleware;

use App\Http\Controllers\V1\MaintainController;
use Closure;

class CheckMaintain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 验证请求接口
        $check = MaintainController::getApiMaintain();
        if ($check) {
            return $check;
        }

        return $next($request);
    }
}