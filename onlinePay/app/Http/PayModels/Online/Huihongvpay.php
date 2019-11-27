<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huihongvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0;  //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /*    */
    public static $reqData = [];

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //self::$resType = 'other';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something

        $data = array();
        $data['mch_id'] = $payConf['business_num'];//商户号
        $data['out_trade_no'] = $order;//商户订单号
        $data['total_fee'] = sprintf("%.2f", $amount);//充值金额 ( 单位元，两位小数 )
        $data['notify_url'] = $ServerUrl;//结果通知地址
        $data['return_url'] = $returnUrl;//同步回调地址
        $data['bank_id'] = $bank;//支付类型

        $data['apply_date'] = date('YmdHis');//付款时间
        $signStr = self::getSignStr($data, false, true);
        $signStr .= '&key=';
        $sign = strtoupper(self::getMd5Sign($signStr, $payConf['md5_private_key']));
        $data['attach'] = self::randStr();//额外数据
        $data['body'] = "VIP";//支付类型
        $data['sign'] = $sign;//签名

        unset($reqData);
        return $data;
    }

    //随机数
    public static function randStr($length = 6)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }



}