<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xintongjuhepay extends ApiModel
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
     * @param array       $reqData 接口传递的参数
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
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        self::$unit = 2;
        //TODO: do something
        $data['merId'] = $payConf['business_num']; //商户号
        $data['outTradeNo'] = $order;
        $data['body'] = 'test';
        $data['notifyUrl'] = $ServerUrl;
        $data['callbackUrl'] = $returnUrl;
        $data['totalFee'] = $amount * 100; //金额为分
        $data['payChannel'] = $bank;
        $data['ipAddress']= self::getIp();//用户终端IP
        $data['nonceStr'] = time();//随机字符串
        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = md5($signStr . '&key=' . $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data                = $request->all();
        $res['totalFee']    = $data['totalFee'] / 100;
        $res['outTradeNo'] = $data['outTradeNo'];

        return $res;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['attach']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = md5($signStr . '&key=' . $payConf['md5_private_key']);

        if (strtolower($sign) == strtolower($mySign) && $data['status'] == 'paid') {
            return true;
        } else {
            return false;
        }
    }
}