<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Encryption\Encrypter;

class DatabasePwd
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
        $crypt = new Encrypter(hex2bin(config('app.cbc')), config("app.cipher"));
        foreach (config('database.connections') as $key => $config) {
            if (!empty($config['username'])) {
                config([
                    "database.connections.{$key}.username" => $crypt->decrypt($config['username'])
                ]);
            }
            if (!empty($config['password'])) {
                config([
                    "database.connections.{$key}.password" => $crypt->decrypt($config['password'])
                ]);
            }
        }

        return $next($request);

    }
}
