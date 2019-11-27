<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinscpay extends ApiModel
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

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['appid'] = $payConf['business_num']; //商户号
        $data['price'] = $amount * 100; //金额
        $data['type'] = '1';
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
            $data['type'] = '0';
        }

        $data['goodsname']     = 'alipay';
        $data['down_trade_no'] = $order; //商户订单号
        $data['backurl']       = $ServerUrl;
        $string                = self::getSignStr($data, false, true);
        $data['sign']          = md5($string . '&signkey=' . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '1') {
            if(self::$isAPP == true){
                $data['payUrl'] = $data['data']['qrcode'];
            }else{
                $data['qrCode'] = $data['data']['qrcode'];
            }
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        parse_str($res, $data);
        if (isset($data['price'])) {
            $data['price'] = $data['price'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $res = file_get_contents("php://input");
        parse_str($res, $data);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, false, true);
        $signTrue = md5($string . '&signkey=' . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }


}