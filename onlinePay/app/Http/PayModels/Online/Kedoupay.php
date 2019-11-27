<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kedoupay extends ApiModel
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

        $ids   = explode('@', $payConf['business_num']);
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$unit = 2;

        //TODO: do something
        $data['mchId']       = $ids[0];
        $data['appId']       = $ids[1];
        $data['productId']     = $bank;
        $data['mchOrderNo']     = $order;
        $data['currency']     = 'cny';
        $data['amount']     = $amount*100;
        $data['notifyUrl']   = $ServerUrl;
        $data['subject']   = 'xxpay测试商品1';
        $data['body']      = 'xxpay测试商品描述';
        $data['extra']      = '{"openId":"o2RvowBf7sOVJf8kJksUEMceaDqo"}';
        $signStr             = self::getSignStr($data,true,true);
        $data['sign']        = md5($signStr . '&key=' . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $arr =json_decode($response,true);
        if($arr['retCode'] == 'SUCCESS'){
            $data['payUrl'] = $arr['payParams']['payUrl'];
        }else{
            $data['retCode'] = $arr['retCode'];
            $data['retMsg'] = $arr['retMsg'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }



    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = md5($signStr . '&key=' . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['status'] == '2') {
            return true;
        } else {
            return false;
        }
    }

}
