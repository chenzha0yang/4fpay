<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Nongpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 异步通知地址
        //TODO: do something
        self::$unit = '2';
        $data = [];
        $data['merchantId'] = $payConf['business_num']; //商户号
        $data['merOrderId'] =  $order;                  //商户订单号
        $data['txnAmt']     =  $amount*100;             //交易单位为分
        $data['frontUrl']   = $returnUrl;               //前台通知地址
        $data['backUrl']    = $ServerUrl;               //后台通知地址
        $data['subject']    = 'subject';                //商品标题
        $data['body']       = 'body';                   //商品描述
        if ($payConf['pay_way'] == '1') {
            $data['gateway'] = 'bank';                  //网关
            $data['bankId']  = $bank;
            $data['dcType']  = '0';
        } else {
            if ($payConf['is_app'] == 1) {
                self::$isAPP = true;
            }
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            $data['gateway'] = $bank;                   //网关
        }
        
        $md5str = self::getSignStr($data, false, true);
        $signStr = $md5str . $payConf['md5_private_key'];
        $data['signature']  = base64_encode(md5($signStr, TRUE));
        $data['signMethod'] = 'MD5';
        $data['subject']    = base64_encode('subject');  //商品标题
        $data['body']       = base64_encode('body');     //商品描述
        unset($reqData);
        return $data;
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $backField = trans("backField.{$type}");
        $data = self::$reqData;
        $sign = $data['signature'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $signValue = self::getMd5Sign("{$signStr}key=", $payConf['private_key']);
        $signBase = base64_encode($signValue, TRUE);
        if ( $data['respCode'] == '1001' && $signBase == $sign ) {
            return true;
        } else {
            return false;
        }
    }
}