<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhonlianvezpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$isAPP   = true;

        $data                 = [];
        $data['service']      = $bank; //接口类型
        $data['version']      = '1.0'; //版本号
        $data['charset']      = 'UTF-8'; //字符集
        $data['sign_type']    = 'MD5';
        $data['merchant_id']  = $payConf['business_num']; //商户号
        $data['out_trade_no'] = $order; //订单
        $data['goods_desc']   = 'vivo';
        $data['total_amount'] = sprintf("%1\$.2f", $amount);
        $data['notify_url']   = $ServerUrl;
        $data['nonce_str']    = md5(time());
        $signStr              = self::getSignStr($data, true, true);
        $sign                 = $signStr . '&key=' . $payConf['md5_private_key'];
        $data['sign']         = strtoupper(md5($sign)); //签名

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data= json_decode($arr,true);
        return $data;
    }


    public static function SignOther($type, $data, $payConf)
    {
        $res  = file_get_contents('php://input');
        $data = json_decode($res, true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key'])); //签名
        if ($sign == $mySign && $data['pay_result'] == '0') {
            return true;
        }
        return false;
    }
}