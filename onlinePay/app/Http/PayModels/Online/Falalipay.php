<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Falalipay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
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
		
		
		$data['uid']           = $payConf['business_num'];
		$data['price']         = $amount;
		$data['paytype']       = $bank;
		$data['user_order_no'] = $order;
		$data['notify_url']    = $ServerUrl;
		$data['return_url']    = $returnUrl;
		$data['sign'] = md5($data['uid'] . $data['price'] . $data['paytype'] . $data['notify_url'] . $data['return_url'] . $data['user_order_no'] . $payConf['md5_private_key']);
		
		unset($reqData);
		return $data;
	}
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->getContent();
		$res =  json_decode($arr,true);
		$data['user_order_no'] = $res['user_order_no'];
		$data['price'] = $res['price'];
		return $data;
	}
	
	
	
	public static function SignOther($type, $data, $payConf)
	{
		$json = file_get_contents('php://input');
		$data = json_decode($json,true);
		echo "success";
		$orderNo  = $data["user_order_no"]; //商户订单号
		$money = $data['price'];
		$sign = md5($orderNo . $data["orderno"] . $data["tradeno"] . $money . $data["realprice"] . $payConf['md5_private_key']);
		if ($sign == $data['sign']) {
			return true;
		} else {
			return false;
		}
	}
	
}