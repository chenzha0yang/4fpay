<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\Logs\RequestLogsController;
use Closure;

class RequestLog
{
    /**
     * 操作日志
     * @param         $request
     * @param Closure $next
     * @return bool|mixed
     */
    public function handle($request, Closure $next)
    {
        RequestLogsController::requestLogAdd($request);
        return $next($request);
    }
}
