<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yiyunhpay extends ApiModel
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
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$unit = '2';

        $data = [];
		if ($payConf['pay_way'] == '1') {
			$data['service'] = "bankPay";                   //	接口名称  固定值：
			$data['pageUrl'] = $returnUrl;
			$data['payChannelType'] = 1;
		} else {
			$data['service'] = "getCodeUrl";                //	接口名称  固定值：
		}

		$data['version'] = "V2.0";                          //	网关版本  固定值：V2.0。
		$data['merchantNo'] = $payConf['business_num'];     //	商户编号  商户在的唯一标识
		$data['payChannelCode'] = $bank;                    //	支付通道编码  填写第三方支付通道
		$data['orderNo'] = $order;                          //	商户订单号  商户系统产生的唯一订单号
		$data['orderAmount'] = $amount*100;                 // 	商户订单金额  以"分"为单位的整型，必须大于零
		$data['curCode'] = "CNY";                           //	交易币种  目前只支持人民币固定值：CNY
		$data['orderTime'] = date('YmdHis');                //	订单时间  格式：YYYYMMDDHHMMSS
		$data['orderSource'] = 1;                           //	订单来源 	订单来源：1PC 2手机
		$data['bgUrl'] = $ServerUrl;                        // 	服务器接收支付结果的后台地址
		$data['signType'] = 1;                              //	签名类型	1:MD5, 2:RSA
        $signStr = self::getSignStr($data, false, true);
        $data["sign"] = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }
}