<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yihuipay extends ApiModel
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

//        if ($payConf['pay_way'] != 1) {
//            self::$reqType = 'curl';
//            self::$payWay = $payConf['pay_way'];
//            self::$resType = 'other';
//        }
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something

        $data = array();
        $data['app_id'] = $payConf['business_num'];//商户ID,请在后台获取
        $data['version'] = '1.0';//版本

        $content = array(
            'out_trade_no' => $order,//商户订单号
            'order_name' => 'juhe',//商品描述
            'total_amount' => sprintf("%.2f", $amount),//总金额
            'spbill_create_ip' => '127.0.0.1',//用户端ip
            'notify_url' => $ServerUrl,//异步回调地址
            'return_url' => $returnUrl,//同步回调地址
        );

        if ($payConf['pay_way'] == 6) {
            $content['channel_type'] = '07'; //07-互联网  08-移动端
            $content['subject'] = 'juhepay'; //订单标题
            $content['bank_code'] = $bank;   //银行编码
            $data['method'] = 'gateway';     //支付方式
        } else {
            $data['method'] = $bank;//支付方式
        }

        $sysParams = array_merge(['content' => json_encode($content)], $data);//合并数组
        $signStr = self::getSignStr($sysParams, true, true);
        $signStr .= "&key=";
        $sign = self::getMd5Sign($signStr, $payConf['md5_private_key']);
        $data['content'] = json_encode($content);
        $data['sign'] = $sign;
        $data['sign_type'] = "MD5";

        unset($reqData);
        return $data;
    }


}