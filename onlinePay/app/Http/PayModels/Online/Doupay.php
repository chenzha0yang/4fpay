<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Doupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data = [];    

		$data['model'] = 'QR_CODE';//扫码模块名
		$data['merchantCode'] = $payConf['business_num'];//商户号
		$data['outOrderId'] = $order;//订单
		$data['amount'] = $amount*100;
		$data['orderCreateTime'] = date('YmdHis');//商户订单创建时间
		$data['noticeUrl'] = $ServerUrl;
		$data['payChannel'] = $bank;//支付类型
		$data['ip'] = '127.0.0.1';

		$signStr = "amount=".$data['amount']."&merchantCode=".$data['merchantCode']."&noticeUrl=".$data['noticeUrl']."&orderCreateTime=".$data['orderCreateTime']."&outOrderId=".$data['outOrderId']."&payChannel=".$data['payChannel']."&KEY=".$payConf['md5_private_key'];
		$data['sign'] = strtoupper(md5($signStr));//签名    
		// $data['merchantCode'] = $payConf['business_num'];//商户号
		// $data['outOrderId'] = $order;//订单
		// $data['amount'] = $amount*100;
		// $data['orderCreateTime'] = date('YmdHis');//商户订单创建时间
		// $data['noticeUrl'] = $ServerUrl;
		// $data['payChannel'] = $bank;//支付类型
		// $signStr = self::getSignStr($data, false, true);
  //       $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&KEY=", $payConf['md5_private_key']));
		// $data['ip'] = '127.0.0.1';
		// $data['model'] = 'QR_CODE';//扫码模块名
		self::$unit = 2;
		self::$reqType = 'curl';
		self::$payWay = $payConf['pay_way'];
		self::$httpBuildQuery = true;
		self::$resType = 'other';

        unset($reqData);
        return $data;
    }
    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response,true);
        if( $result['code'] == '00' ) {
            $result['pay_params'] = $result['data']['url'];
        }
        return $result;
    }

}