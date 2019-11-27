<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Haiftpay extends ApiModel
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
		
		self::$unit = 2;
		
		$data['merchantId'] = $payConf['business_num'];//商户号
		$data['payment'] = $bank;//银行编码
		$data['payMoney'] = $amount*100;//订单金额
		$data['orderNo'] = $order;//订单号
		$data['orderName'] = 'test';
		$data['notifyUrl'] = $ServerUrl;
		$data['returnUrl'] = $returnUrl;
		$data['version'] = '3.0';
		$signStr = '';
		foreach ($data as $key => $value) {
			$signStr .= $value.'&';
		}
		$data['sign'] = strtoupper(md5($signStr .$payConf['md5_private_key']));
		$data['mode'] = 'form';
		
		unset($reqData);
		return $data;
	}
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->all();
		$data['payMoney'] = $arr['payMoney'] / 100;
		$data['merOrderNo'] = $arr['merOrderNo'];
		return $data;
	}
	
	
	public static function SignOther ($mod, $data, $payConf)
	{
		$sign = $data['sign'];
		unset($data['sign']);
		$signStr =  $data['payOrderId'].'&'.$data['merOrderNo'].'&'.$data['payment'].'&'.$data['payMoney'].'&'.$data['actualPay'].'&'.$data['orderName'].'&'.$data['status'].'&'.$data['successTime'];
		$signTrue = strtoupper(md5($signStr . "&" . $payConf['md5_private_key']));
		if ($sign == $signTrue & $data['status'] == 2) {
			return true;
		} else {
			return false;
		}
	}
	
}