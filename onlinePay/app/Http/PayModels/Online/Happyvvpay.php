<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Happyvvpay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组
	
	public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query
	
	public static $isAPP = false; // 判断是否跳转APP 默认false
	
	public static $changeUrl = false;
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
		$ServerUrl = $reqData['ServerUrl']; // 异步通知地址
		$returnUrl = $reqData['returnUrl']; // 同步通知地址
		self::$changeUrl = true;
		//TODO: do something
		$data             = [
			'body'        => 'goods_desc',              //商品描述
			'charset'     => 'utf-8',                   //字符集
			'defaultbank' => $bank,                     //支付方式
			'isApp'       => 'web',                     //接入方式
			'merchantId'  => $payConf['business_num'],  //商户号
			'notifyUrl'   => $ServerUrl,                //异步通知地址
			'orderNo'     => $order,                    //订单号
			'paymentType' => '1',                       //支付类型 - 固定值1
			'paymethod'   => 'bankPay',               //支付方式
			'returnUrl'   => $returnUrl,                //支付成功跳转
			'service'     => 'online_pay',              //网上支付 - 固定值
			'title'       => 'goods_name',              //商品名称
			'totalFee'    => $amount,                   //金额
		];
		if(self::$payWay == 6){
			$data['paymethod'] = 'directPay';
		}
		$signStr          = self::getSignStr($data, true, true);
		$data['sign']     = strtoupper(sha1("{$signStr}" . $payConf['md5_private_key']));
		$data['signType'] = "SHA";
		$url = $reqData['formUrl'] . '/payment/v1/order/' . $data['merchantId'] . '-' . $data['orderNo'];
		$post['queryUrl'] = $url;
		$post['data'] = $data;
		unset($reqData);
		return $post;
	}
	
	public static function SignOther($type, $data, $payConf)
	{
		$mySign = $data['sign'];
		unset($data['sign'], $data['signType']);
		$signStr          = self::getSignStr($data, true, true);
		$sign     = strtoupper(sha1("{$signStr}" . $payConf['md5_private_key']));
		if ($sign == strtoupper($mySign) && $data['trade_status'] == 'TRADE_FINISHED') {
			return true;
		} else {
			return false;
		}
	}
	
}