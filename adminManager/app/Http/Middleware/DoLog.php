<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\Logs\DoLogController;
use Closure;

class DoLog
{
    /**
     * 操作日志
     * @param $request
     * @param Closure $next
     * @return bool|mixed
     */
    public function handle($request, Closure $next)
    {
        DoLogController::createDoLogs($request);
        return $next($request);
    }
}
