<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\APIController;
use App\Extensions\RedisConPool;
use App\Client;
use Illuminate\Http\Request;

class TokenController extends APIController
{
    /**
     * 获取Token
     *
     * @param Request $request
     * @return string
     */
    public function getToken(Request $request)
    {
        $order    = self::checkParameter($request, 'order', "/^[A-Za-z0-9]+$/");
        $agentId  = self::checkParameter($request, 'agentId', "/^[A-Za-z0-9]{1,50}$/");
        $agentNum = self::checkParameter($request, 'agentNum', "/^[A-Za-z0-9]{1,10}$/");
        $key      = $order . $agentId . $agentNum . time() . rand(1000, 9999);
        $token    = md5($key);
        $status   = RedisConPool::setEx($order, 120, $token);

        $response = array(
            'status' => $status,
            'token'  => $token,
        );

        return response()->json($response);
    }

    /**
     * 验证Token
     *
     * @param $getToken
     * @param $sysTokenKey
     * @return bool
     */
    public static function checkToken($getToken, $sysTokenKey)
    {
        $token = RedisConPool::get($sysTokenKey);

        if (strcmp($token, $getToken) == 0) {
            return true;
        }

        return false;
    }

    /**
     * 支付参数验签
     *
     * @param Request $request
     * @return bool
     */
    public static function checkPaySign(Request $request)
    {
        $data['order']       = self::checkParameter($request, 'order', "/^[A-Za-z0-9]+$/");
        $data['agentId']     = self::checkParameter($request, 'agentId', "/^[A-Za-z0-9]{1,50}$/");
        $data['agentNum']    = self::checkParameter($request, 'agentNum', "/^[A-Za-z0-9]{1,10}$/");
        $data['amount']      = self::checkParameter($request, 'amount', "/^\d+([.]\d{1,2})?$/");
        $data['bank']        = self::checkParameter($request, 'bank', '', false);
        $data['payWay']      = self::checkParameter($request, 'payWay', "/^\d+$/");
        $data['merchantId']  = self::checkParameter($request, 'merchantId', "/^\d+$/");
        $data['businessNum'] = self::checkParameter($request, 'businessNum', "");
        $data['notifyUrl']   = self::checkParameter($request, 'notifyUrl', "", false);
        $data['isApp']       = self::checkParameter($request, 'isApp', "", false);
        $data['cardMoney']   = self::checkParameter($request, 'cardMoney', "", false);
        $data['cardNumber']  = self::checkParameter($request, 'cardNumber', "", false);
        $data['cardPwd']     = self::checkParameter($request, 'cardPwd', "", false);
        $clientUserId        = self::checkParameter($request, 'clientUserId', "/^\d+$/", true);
        $pkSign              = self::checkParameter($request, 'sign');

        self::$client = Client::getInstance($clientUserId)->info();
        if (empty(self::$client)) {
            return false;
        }
        $data['clientSecret']      = self::$client->secret;
        self::$PKData['client_id'] = self::$client->user_id;
        ksort($data);
        $str = '';
        foreach ($data as $key => $val) {
            if (!is_null($val) && $val !== '') {
                $str .= "&&&{$key}=#{$val}#&&&";
            }
        }
        $data['username']    = self::checkParameter($request, 'username', "", false);
        unset($data);
        $sign = md5(md5($str));
        if (strtoupper($sign) == strtoupper($pkSign)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * out参数验签
     *
     * @param Request $request
     * @return bool
     */
    public static function checkOutSign(Request $request)
    {
        $data['order']       = self::checkParameter($request, 'order', "/^\d+$/");
        $data['agentId']     = self::checkParameter($request, 'agentId', "/^\w{1,4}$/");
        $data['agentNum']    = self::checkParameter($request, 'agentNum', "/^\w$/");
        $data['amount']      = self::checkParameter($request, 'amount', "/^\d+([.]\d{1,2})?$/");
        $data['bankCode']    = self::checkParameter($request, 'bankCode', "/^[a-zA-Z\d]*$/i"); // 银行编码
        $data['bankCard']    = self::checkParameter($request, 'bankCard', ""); // 银行卡号
        $data['bankName']    = self::checkParameter($request, 'bankName', '', false); // 支行名称
        $data['cardName']    = self::checkParameter($request, 'cardName'); // 真实姓名
        $data['notifyUrl']   = self::checkParameter($request, 'notifyUrl', "");
        $data['merchantId']  = self::checkParameter($request, 'merchantId', "/^\d+$/");
        $data['businessNum'] = self::checkParameter($request, 'businessNum');
        $data['province']    = self::checkParameter($request, 'province');
        $data['city']        = self::checkParameter($request, 'city');

        $clientUserId = self::checkParameter($request, 'clientUserId', "/^\d+$/", true);
        $pkSign       = self::checkParameter($request, 'sign');

        self::$client = Client::getInstance($clientUserId)->info();
        if (empty(self::$client)) {
            return false;
        }
        $data['clientSecret'] = self::$client->secret;

        ksort($data);
        $str = '';
        foreach ($data as $key => $val) {
            if (!is_null($val) && $val !== '') {
                $str .= "&&&{$key}=#{$val}#&&&";
            }
        }
        $data['username']        = self::checkParameter($request, 'username', "", false);
        unset($data);
        $sign = md5(md5($str));
        if (strtoupper($sign) == strtoupper($pkSign)) {
            return true;
        } else {
            return false;
        }
    }


}