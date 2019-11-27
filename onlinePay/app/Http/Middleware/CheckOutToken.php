<?php

namespace App\Http\Middleware;

use App\Http\Controllers\V1\TokenController;
use Closure;

class CheckOutToken
{

    protected static $response = [
        'status' => false,
        'code'   => 1014,
        'data'   => '',
    ];

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
            self::$response['data'] = trans('error.outTokenError');
            return response()->json(self::$response);
        }
        $passToken = TokenController::checkToken($tokSign, $order);

        if (!$passToken) {
            self::$response['data'] = trans('error.outTokenError');
            return response()->json(self::$response);
        }

        $passSign = TokenController::checkOutSign($request);

        if (!$passSign) {
            self::$response['data'] = trans('error.outTokenError');
            return response()->json(self::$response);
        }

        return $next($request);
    }

}