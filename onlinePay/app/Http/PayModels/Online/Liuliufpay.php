<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Liuliufpay extends ApiModel
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
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        $data['userOrderId'] = $order; //订单号
        $data['companyId']   = $payConf['business_num']; //商户号
        $data['item']        = 'good';
        $data['fee']         = $amount * 100; //金额
        $data['syncUrl']     = $ServerUrl; //异步回调
        $data['callbackUrl'] = $returnUrl; //同步回调
        if ((int)$payConf['pay_way'] === 1) {
            $data['payType']  = '20';
            $data['bankCode'] = $bank;
        } else {
            $data['payType'] = $bank; //支付方式
        }
        $data['ip']          = self::getIp(); //下单ip
        $data['sign']        = strtolower(md5($data["companyId"] . "_" . $data["userOrderId"] . "_" . $data["fee"] . "_" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['result'] == '0') {
            if(self::$payWay == 4){
                $data['qrCode'] = $data['qrData'];
            }else{
                $data['qrCode'] = $data['param'];
            }
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['fee'])) {
            $arr['fee'] = $arr['fee'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        $signTrue    = md5($data["companyId"] . "_" . $data["userOrderId"] . "_" . $data["fee"] . "_" . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)  && $data['payResult'] == '0') {
            return true;
        }
        return false;
    }


}