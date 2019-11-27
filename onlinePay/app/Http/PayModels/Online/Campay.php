<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Campay extends ApiModel
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
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;

        date_default_timezone_set('Asia/Shanghai');
        $data = array();
        if ($payConf['pay_way'] == '1') {
            $data['service'] = 'pay.b2c';
        } else {
            $data['service'] = $bank;
        }
        $data['version']    = '1.0';//接口版本
        $data['merchantId'] = $payConf['business_num']; //商家号
        $data['orderNo']    = $order; //商户网站唯一订单号
        $data['tradeDate']  = date("Ymd", time());; //商户交易日期
        $data['tradeTime'] = date("His", time());//商户交易时间
        $data['amount']    = $amount * 100; //商户订单总金额
        $data['cellPhone'] = '';
        $data['clientIp']  = '35.194.220.251'; //客户端IP
        $data['notifyUrl'] = $ServerUrl; //服务器异步通知地址
        $data['attach']    = '';
        $data['key']       = $payConf['md5_private_key'];
        $signPars          = "";
        ksort($data);
        foreach ($data as $k => $v) {
            if (!empty($v)) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars     = rtrim($signPars, '&');
        $data['sign'] = strtolower(md5($signPars));//签名

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['amount'])) {
            $arr['amount'] = $arr['amount'] / 100;
        }
        return $arr;
    }

    public static function signOther($mod, $data, $payConf)
    {
        $data['key'] = $payConf['md5_private_key'];
        $signPars = "";
        ksort($data);
        foreach ($data as $k => $v) {
            if ($v !='' && $k != 'sign') {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars = rtrim($signPars,'&');
        $signStr = md5($signPars);//签名
        if ($data['sign'] == $signStr) {
            return true;
        }
        return false;
    }
}
