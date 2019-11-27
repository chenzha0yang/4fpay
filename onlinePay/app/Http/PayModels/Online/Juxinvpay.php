<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Juxinvpay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType
	
	public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query
	
	public static $isAPP = false; // 判断是否跳转APP 默认false
	
	private static $UserName = '';
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
		
		//TODO: do something
		$data['app_id']          = $payConf['business_num'];
		$data['price']        = sprintf('%.2f', $amount);
		$data['type']      = $bank;
		$data['out_order_id'] = $order;
		$data['IP']       = self::getIp();
		$data['notifyurl'] = $ServerUrl;
		$data['returnurl'] = $returnUrl;
		$signStr             = md5('key1='.$data['app_id'] . '&key2='.$data['price'] . '&key3='.$data['out_order_id'] .'&key4='. $data['type'] . '&key5='.$data['notifyurl']);
		
		$data['sign']        = md5($signStr. 'key=' . $payConf['md5_private_key']);
		
		unset($reqData);
		return $data;
	}
	
	
	public static function SignOther($type, $data, $payConf)
	{
		$sign     = $data['sign'];
		$signStr             = md5('key1='.$data['order_id'] . '&key2='.$data['out_order_id'] . '&key3='.$data['price'] .'&key4='. $data['realprice'] . '&key5='.$data['type']);
		$signTrue       = md5($signStr. 'key=' . $payConf['md5_private_key']);
		if (strtolower($sign) == strtolower($signTrue)) {
			return true;
		}
		return false;
	}
	
	
}