<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bajiupay extends ApiModel
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
        $data['tradeType'] = 'cs.pay.submit';//交易类型
        $data['version'] = '2.0';//版本号
        $data['channel'] = $bank;//支付渠道
        if($payConf['pay_way'] == '1'){
            $data['channel'] = 'union_pay';//支付渠道
        }
        $data['mchNo'] = $payConf['business_num'];//商户号
        $data['body'] = 'vivo';//商品描述
        $data['mchOrderNo'] = $order;//订单
        $data['amount'] = strval($amount*100);
        $data['currency'] = 'CNY';
        $data['timePaid'] = date("YmdHis");//订单生成时间
        $data['remark'] = 'online-pay';

        $extra['notifyUrl'] = $ServerUrl;//后台通知地址
        if($payConf['pay_way'] == '1'){
            $extra['callbackUrl'] = $returnUrl;//页面返回地址
            $extra['memberId'] = $payConf['business_num'];//买家用户标识
            $extra['bankType'] = $bank;//银行编码
            $extra['cardType'] = '0';//银行卡类型
            $extra['merchantName'] = 'PYJY';//商户展示名称
            $extra['orderPeriod'] = '30';//订单有效时长
        }else{
            $extra['payUserName'] = get_session('username');
            $extra['clientIp'] = self::getIp();
        }
        $params = array_merge($data,$extra);
        $signStr = self::getSignStr($params, false, true);
        $postKey = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        $data["sign"] = $postKey;
        $data['extra'] = json_encode($extra);
        self::$method = 'get';
        self::$reqType = 'curl';
        self::$unit = 2;

        unset($reqData);
        return $data;
    }
}