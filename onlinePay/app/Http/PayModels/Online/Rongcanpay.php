<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Rongcanpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        $data = [];
        $data['charset']          = 'UTF-8';//编码方式
        $data['merchantCode']     = $payConf['business_num']; //商家号
        $data['orderNo']          = $order; //商户网站唯一订单号
        $data['amount']           = strval($amount*100); //商户订单总金额
        $data['channel']  = 'BANK';
        $data['bankCode'] = $bank;
        if($payConf['pay_way'] == '1'){
            $data['channel']  = 'BANK';
            $data['bankCode'] = 'CASHIER';
        }
        $data['remark']           = 'product'; //商品名称
        $data['notifyUrl']        = $ServerUrl; //服务器异步通知地址
        $data['returnUrl']        = $returnUrl;
        $data['extraReturnParam'] = $order;;
        $str = self::getSignStr($data,true,false);
        $sign = self::getRsaSign($str,$payConf['rsa_private_key']);
        $data['signType'] = 'RSA';//签名方式
        $data['sign']      = $sign;//签名
        self::$unit = 2;
        unset($reqData);
        return $data;
    }
}