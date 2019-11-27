<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shilianpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        $data = array(
            'mch_id'            => $payConf['business_num'],   //商户号
            'trade_type'        => $bank,
            'nonce'             => self::randStr(30),
            'timestamp'         => time(),
            'subject'           => 'VIP',
            'out_trade_no'      => $order,
            'total_fee'         => $amount * 100,
            'spbill_create_ip'  => request()->ip(),
            'notify_url'        => $ServerUrl,
            'return_url'        => $returnUrl,
            'sign_type'         => 'MD5',
        );
        $signStr      = self::getSignStr($data, true,true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'])); //MD5签名

        unset($reqData);
        return $data;
    }

       /**
     * @param $request
     * @return array
     */
    public static function getVerifyResult($request)
    {
        $res = $request->all();
        $res['total_fee'] = $res['total_fee']/100;
        return $res;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $mysign  = md5($signStr . '&key=' . $payConf['md5_private_key']);
        if (strtolower($sign) == strtolower($mysign)  && $data['result_code'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}