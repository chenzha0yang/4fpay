<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Extensions\Code;
use App\Extensions\Curl;
use App\Http\Controllers\AdminController;
use Illuminate\Encryption\Encrypter;

class OrderLogsController extends AdminController
{
    /**
     * 回调日志
     *
     */
    public function orderLogSelect()
    {
        $get = self::requestParam(__FUNCTION__);
        $ip = env('LOGS_IP'); // ip
        $port = env('LOGS_PORT'); // 端口
        $path = env('LOGS_PATH'); // 路由
        $username = env('LOGS_USERNAME'); // 账号
        $password = env('LOGS_PASSWORD'); // 密码
        $crypt = new Encrypter(hex2bin(config('app.cbc')), config('app.cipher'));
        if(!$username || !$password){
            return $this->responseJson(Code::URL_NOT_FOUND);
        }
        $username = $crypt->decrypt($username);
        $password = $crypt->decrypt($password);
        //处理传入时间
        $date = str_replace('-', '', substr($get['date'], 0, 10));
        $url = "http://{$username}:{$password}@{$ip}:{$port}{$path}{$get['orderNumber']}/{$date}";
        Curl::$url = $url;
        Curl::$method = '';
        $result = Curl::sendRequest();
        return $this->responseJson([$result]);
    }
}
