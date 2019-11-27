<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Liyingpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

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

        $data                 = array();
        $data['mch_id']       = $payConf['business_num']; //商户号
        $data['trade_type']   = $bank; //交易类型
        $data['out_trade_no'] = $order; //商户订单号
        $data['total_fee']    = $amount*100; //金额，以分为单位
        $data['bank_id']      = '';
        $data['notify_url']   = urlencode($ServerUrl); //通知地址
        $data['return_url']   = urlencode($returnUrl); //通知地址
        $data['time_start']   = date('YmdHis'); //订单生成时间
        $data['nonce_str']    = self::randStr(30); //随机英文及数字的字符串，不长于 32 位
        $md5str               = self::getSignStr($data, false, true);
        unset($data['notify_url']);
        unset($data['return_url']);
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $data['body']       = 'body';
        $data['attach']     = 'attach';
        $data['sign']       = strtoupper(md5($md5str . "&key=" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $data['total_fee'] = $data['total_fee'] / 100;
        return $data;
    }
}
