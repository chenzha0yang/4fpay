<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fuguizpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $ispc = '';

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

        //TODO: do something
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType        = 'other';
        self::$isAPP          = true;
        if ($payConf['is_app'] == 1) {
            self::$ispc = 1;
        }

        $data               = [];
        $data['uid']        = $payConf['business_num'];
        $data['istype']     = $bank;
        $data['notify_url'] = $ServerUrl;
        $data['orderid']    = $order;
        $data['orderuid']   = $order;
        $data['goodsname']  = $amount;
        $data['key']        = md5($data['goodsname'] . $data['istype'] . $data['notify_url'] . $data['orderid'] . $data['orderuid'] . $payConf['md5_private_key'] . $data['uid']);
        unset($reqData);
        return $data;
    }

    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);
        if ($result['code'] == 1) {
            if (self::$ispc = 1) {
                $result['pay_url'] = $result['data']['pay_url'];
            } else {
                $result['pay_url'] = $result['data']['qrcode'];
            }
        }
        return $result;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign     = $data['key'];
        $signTrue = md5($data['orderid'] . $data['orderuid'] . $data['platform_trade_no'] . $data['price'] . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }
}