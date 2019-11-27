<?php

namespace App\Http\Middleware;

use App\Http\Controllers\V1\TokenController;
use Closure;

class CheckToken
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
        if ($request->has('order') && $request->has('tokSign')) {
            $order   = $request->input('order');
            $tokSign = $request->input('tokSign');
        } else {
            return redirect('/tokenErr?type=1');
        }
        $passToken = TokenController::checkToken($tokSign, $order);

        if (!$passToken) {
            return redirect('/tokenErr?type=2');
        }

        $passSign = TokenController::checkPaySign($request);
        if (!$passSign) {
            return redirect('/tokenErr?type=3');
        }

        return $next($request);
    }

}