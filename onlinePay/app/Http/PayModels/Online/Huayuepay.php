<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huayuepay extends ApiModel
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
		
		$data['orderAmount'] = $amount;
		$data['orderId']     = $order;
		$data['merchant']    = $payConf['business_num'];
		if ($payConf['pay_way'] == 2) {
			$data['payMethod'] = '1';
		} elseif ($payConf['pay_way'] == 3) {
			$data['payMethod'] = '2';
		} else {
			$data['payMethod'] = $bank;
		}
		$data['payType']  = $bank;
		$data['version']  = '1.0';
		$data['signType'] = 'MD5';
		$data['outcome']  = 'no';
		ksort($data);
		$signStr             = http_build_query($data);
		$data['sign']        = strtoupper(md5($signStr . $payConf['md5_private_key']));
		$data['notifyUrl']   = $ServerUrl;
		$data['createTime']  = time();
		$data['returnUrl']   = $returnUrl;
		
		unset($reqData);
		return $data;
	}
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->getContent();
		$data = json_decode($arr, true);
		$data['orderAmount'] = $data['paramsJson']['data']['orderAmount'];
		$data['orderId'] = $arr['paramsJson']['data']['orderId'];
		return $data;
	}
	
	public static function SignOther($type, $data, $payConf)
	{
		$sign     = $data['sign'];
		$signTrue = strtoupper(md5($payConf['md5_private_key'] . md5(base64_encode(json_encode($data['paramsJson'])))));
		if (strtolower($sign) == strtolower($signTrue) && $data['paramsJson']['code'] == '000000') {
			return true;
		} else {
			return false;
		}
	}
}