<?php
/**
 * 中间件验证 API
 *
 */

namespace App\Http\Middleware;

use App\Extensions\ClientCheck;
use Closure;

class CheckClient
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
        $checkCli = new ClientCheck($request);
        $check = $checkCli->checkClient();
        if ($check !== true) {
            return $check;
        }

        return $next($request);
    }
}
