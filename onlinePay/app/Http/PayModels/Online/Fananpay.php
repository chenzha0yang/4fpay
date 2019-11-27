<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fananpay extends ApiModel
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

        //TODO: do something
        $data = array();

        $data['app_id'] = $payConf['business_num'];//分配给商户的APPIDd68f0e7f6b6b46f42d46838dec9721d2
        $data['create_time'] = date('YmdHis');//发起请求时间
        $data['out_trade_no'] = $order;//商户交易订单号
        $data['subject'] = 'zhif';//订单标题
        $data['total_amount'] = $amount * 100;//总金额

        $data['body'] = 'aaaa';//订单详情内容
        $data['return_url'] = $returnUrl;//返回地址
        $data['notify_url'] = $ServerUrl;//通知地址
        if($payConf['pay_way'] == '1'){
            $data['pay_type'] = '80003';//支付方式
            $data['bank_code'] = $bank;//银行编码
        }else{
            $data['pay_type'] = $bank;//支付方式
        }

        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = self::getRsaSign($signStr, $payConf['rsa_private_key']);
        $data['type'] = 3;//返回类型
        self::$unit = 2;
        return $data;
    }

    /**
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $result = trans("backField.{$mod}");
        $dinPaySign = $data["sign"];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $true = self::verifyRSA($signStr, $dinPaySign, $payConf['public_key'],OPENSSL_ALGO_SHA256);
        if ($true) {
            if ($data[$result['orderStatus'][0]]  == $result['orderStatus'][1]) {
                return true;
            }
        }
        return false;
    }

}