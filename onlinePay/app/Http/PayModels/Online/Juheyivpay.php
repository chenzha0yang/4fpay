<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Juheyivpay extends ApiModel
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
		
		$data['account_id']   = $payConf['business_num']; //商户id
		$data['content_type'] = 'text'; //返回方式
		$data['thoroughfare'] = $bank; //服务版
		
		if ($data['thoroughfare'] == 'service_auto') {
			if ($payConf['pay_way'] == '2') {
				$data['type'] = '1'; //支付类型
			} else {
				$data['type'] = '2';
			}
		}
		
		$data['out_trade_no']     = $order; //订单号
		$data['robin']            = '2'; //轮询模式
		$data['amount']           = sprintf('%.2f', $amount); //金额
		$data['callback_url']     = $ServerUrl; //异步通知地址
		$data['success_url']      = $returnUrl; //同步跳转地址
		$data['error_url']        = $returnUrl; //异常跳转地址
		$data['sign']             = static::sign($payConf['md5_private_key'], ['amount' => $data['amount'], 'out_trade_no' => $data['out_trade_no']]);
		
		unset($reqData);
		return $data;
	}
	
	
	public static function sign($key_id, $array)
	{
		$data = md5(sprintf("%.2f", $array['amount']) . $array['out_trade_no']);
		$key[]       = "";
		$box[]       = "";
		$cipher      = '';
		$pwd_length  = strlen($key_id);
		$data_length = strlen($data);
		for ($i = 0; $i < 256; $i++) {
			$key[$i] = ord($key_id[$i % $pwd_length]);
			$box[$i] = $i;
		}
		for ($j = $i = 0; $i < 256; $i++) {
			$j       = ($j + $box[$i] + $key[$i]) % 256;
			$tmp     = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for ($a = $j = $i = 0; $i < $data_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			
			$tmp     = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipher .= chr(ord($data[$i]) ^ $k);
		}
		return md5($cipher);
	}
	
	public static function SignOther($type, $data, $payConf)
	{
		if (strtolower(self::sign($payConf['md5_private_key'], ['amount' => $data['amount'], 'out_trade_no' => $data['out_trade_no']])) == strtolower($data['sign'])) {
			return true;
		} else {
			return false;
		}
	}
	
}