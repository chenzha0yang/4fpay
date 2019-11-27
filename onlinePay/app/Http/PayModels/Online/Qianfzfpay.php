<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qianfzfpay extends ApiModel
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
		
		
		$data['apikey'] = $payConf['md5_private_key'];
		$data['order_id'] = $order;//订单号
		$data['order_price'] = sprintf('%.2f',$amount);//订单金额
		$data['notify_url'] = $ServerUrl;
		$data['return_url'] = $returnUrl;
		$data['type'] = $bank;//银行编码
		$signStr = self::getSignStr($data,true,true);
		$data['sign'] = strtoupper(md5($signStr));
		$jsonData = json_encode($data,JSON_UNESCAPED_UNICODE);
		$post['content'] = strtoupper(bin2hex(openssl_encrypt($jsonData, 'AES-128-CBC','kafu-ef465sd1000', 1,'5effe26250e19130')));
		
		unset($reqData);
		return $data;
	}
	
	
	public static function SignOther($type, $post, $payConf)
	{
		$content = $post['content'];
		$str = self::hexToStr($content);
		$data = openssl_decrypt($str , 'AES-128-CBC','kafu-ef465sd1000', 1,'5effe26250e19130');
		$sign = $data['sign'];
		unset($data['sign']);
		$signStr =  self::getSignStr($data,true,true);
		$signTrue = strtoupper(md5($signStr));
		if (strtoupper($sign) == $signTrue && $data['status'] == 'ok') {
			return true;
		} else {
			return false;
		}
	}
	
	public static function hexToStr($hex){
		$string="";
		for($i=0;$i<strlen($hex)-1;$i+=2)
			$string.=chr(hexdec($hex[$i].$hex[$i+1]));
		return  $string;
	}
}