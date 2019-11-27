<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Longxinzfpay extends ApiModel
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
		
		$data['version'] = '1.0';
		$data['customerid'] = $payConf['business_num'];
		$data['sdorderno'] = $order;
		$data['total_fee'] = number_format($amount,2,'.','');
		$data['paytype'] = $bank;
		if ($payConf['pay_way'] == 0) {
			$data['paytype'] = 'bank';
			$data['bankcode'] = $bank;
		}
		$data['notifyurl'] = $ServerUrl;
		$data['returnurl'] = $returnUrl;
		$data['remark'] = $amount;
		
		$data['pay_model'] = 9;
		$str = "version={$data['version']}&customerid={$data['customerid']}&total_fee={$data['total_fee']}&sdorderno={$data['sdorderno']}&notifyurl={$data['notifyurl']}&returnurl={$data['returnurl']}&{$payConf['md5_private_key']}";
		$data['sign'] = md5($str);
		unset($reqData);
		return $data;
	}
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->all();
		$data = json_decode(stripslashes($arr['postdata']),true);
		return $data;
	}
	
	/**
	 * @param $type
	 * @param $res
	 * @param $payConf
	 * @return bool
	 */
	public static function SignOther($type, $res, $payConf)
	{
		$data = json_decode(stripslashes($res['postdata']),true);
		$str = "customerid={$data['customerid']}&status={$data['status']}&sdpayno={$data['sdpayno']}&sdorderno={$data['sdorderno']}&total_fee={$data['total_fee']}&paytype={$data['paytype']}&{$payConf['md5_private_key']}";
		$sign = md5($str);
		if (strtolower($sign) == strtolower($data['sign']) && $data['status'] == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	
}