<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Okbpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /**
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭

        //TODO: do something
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;

        self::$isAPP = true;


        $data['appid']       = $payConf['business_num'];
        $data['appkey']      = $payConf['md5_private_key'];
        $data['orderid']     = $order;
        $data['money']       = sprintf('%.2f', $amount);
        $data['paycode']     = $bank;
        $data['notifyurl']   = $ServerUrl;
        $data['returnurl']   = $returnUrl;
        $data['goodsname']   = 'ouhayou';
        $data['remark']      = 'mark';
        $data['orderperiod'] = '30';
        $data['membername']  = $payConf['business_num'];
        $data['timestamp']   = time();
        $signStr             = self::SignStr($data);
        $data['sign_type']   = 'MD5';

        $data['sign'] = md5($signStr);

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $json = $request->all();
        $arr = json_decode(base64_decode($json['data']),true);
        return $arr;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $json = base64_decode($data['data']);
        $arr  = json_decode($json, true);
        $sign = $arr['sign'];
        unset($arr['sign']);
        $arr['appkey'] = $payConf['md5_private_key'];
        $signStr       = self::SignStr($arr);
        $mySign        = md5($signStr);
        if (strtoupper($sign) == strtoupper($mySign)) {
            return true;
        } else {
            return false;
        }


    }

    public static function SignStr($para, $isNull = true, $sort = true, $space = '&')
    {
        if ($sort) {
            ksort($para);
        }
        $arg = "";
        foreach ($para as $key => $value) {
            if ($isNull) {
                if ($value != '' && $value != null) {
                    $arg .= "{$key}=" . urlencode($value) . "{$space}";
                }
            } else {
                $arg .= "{$key}={$value}{$space}";
            }
        }
        //去掉最后一个&字符
        $arg = rtrim($arg, $space);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;

    }
}