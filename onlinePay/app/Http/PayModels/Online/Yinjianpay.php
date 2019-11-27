<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Yinjianpay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType
	
	public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query
	
	public static $isAPP = false; // 判断是否跳转APP 默认false
	
	public static $resData = [];
	
	/**
	 * @param array       $reqData 接口传递的参数
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
		
		//判断是否需要跳转链接 is_app=1开启 2-关闭
		if ($payConf['is_app'] == 1) {
			self::$isAPP = true;
		}
		
		self::$reqType = 'curl';
		self::$payWay  = $payConf['pay_way'];
		self::$unit = 2;
		
		$data['mch_id']           = $payConf['business_num'];
		$data['body']             = $order;
		$data['out_trade_no']     = $order;
		$data['amount']           = $amount * 100;
		$data['fee_type']         = 'CNY';
		$data['spbill_create_ip'] = self::getIp();
		$data['notify_url']       = $ServerUrl;
		$data['return_url']       = $returnUrl;
		if ($payConf['pay_way'] == '1') {
			$data['bank_type']    = $bank;
			$data['payment_type'] = 'trade.gateway';
		} else {
			$data['payment_type'] = $bank;
		}
		$data['nonce_str']   = $order;
		$signStr             = self::getSignStr($data,true, true);
		$data['sign']        = md5($signStr . '&key=' . $payConf['md5_private_key']);
		$data['sign_type']   = 'MD5';
		
		unset($reqData);
		return $data;
	}
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->all();
		$arr['amount'] = $arr['amount'] / 100;
		return $arr;
	}
	
	/**
	 * @param $type
	 * @param $data
	 * @param $payConf
	 * @return bool
	 */
	public static function SignOther($type, $data, $payConf)
	{
		$sign    = $data['sign'];
		unset($data['sign']);
		$signStr      = self::getSignStr($data,true, true);
		$signTrue = md5($signStr . '&key=' . $payConf['md5_private_key']);
		if ($sign == $signTrue && $data['payment_status'] == 'SUCCESS') {
			return true;
		} else {
			return false;
		}
	}
}