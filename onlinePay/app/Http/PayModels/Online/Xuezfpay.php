<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xuezfpay extends ApiModel
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
		
		self::$method = 'get';
		
		$data['param']       = $order;
		$data['seller']      = $payConf['business_num'];
		$data['price']       = $amount;
		$data['type']        = $bank;
		$data['notify_url']  = $ServerUrl;
		$data['return_url']  = $returnUrl;
		$data['goods']       = 'xf';
		$signStr             = "goods={$data['goods']}&notify_url={$data['notify_url']}&param={$data['param']}&price={$data['price']}&return_url={$data['return_url']}&seller={$data['seller']}&type={$data['type']}{$payConf['md5_private_key']}";
		$data['sign']        = md5($signStr);
		
		unset($reqData);
		return $data;
	}
	
	
	public static function SignOther($type, $data, $payConf)
	{
		$signStr = "money={$data['money']}&name={$data['name']}&out_trade_no={$data['out_trade_no']}&pid={$data['pid']}&trade_no={$data['trade_no']}&trade_status={$data['trade_status']}&type={$data['type']}{$payConf['md5_private_key']}";
		$mysign  = md5($signStr);
		$sign    = $data['sign'];
		if (strtolower($mysign) == strtolower($sign) && $data['trade_status'] == 'TRADE_SUCCESS') {
			return true;
		} else {
			return false;
		}
	}
}