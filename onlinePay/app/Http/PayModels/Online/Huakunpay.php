<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huakunpay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType
	
	public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query
	
	public static $isAPP = false; // 判断是否跳转APP 默认false
	
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
		
		if ($payConf['pay_way'] == '0') {
			$type = '6';
		}
		$data['pay_type']   = '2'; //支付类型
		$data['mch_id']     = $payConf['business_num']; //商户标识
		$data['order_id']   = $order; //商户订单编号
		$data['channel_id'] = $type; //支付通道标识
		$data['pay_amount'] = number_format($amount, 2, '.', ''); //支付金额 元 两位小数
		$data['name']       = 'iphone'; //商品名
		$data['explain']    = 'buy'; //商品描述
		$data['remark']     = 'remark'; //订单备注
		$data['result_url'] = $returnUrl; //同步跳转地址
		$data['notify_url'] = $ServerUrl; //异步通知地址
		$data['client_ip']  = self::getIp(); //客户端ip
		$data['bank_code']  = '';
		$data['is_qrimg']   = 'o';
		if ($type == '6') {
			$data['bank_code'] = $bank; //网银编码
		} else {
			$data['is_qrimg'] = '1'; //启用二维码图片. "仅在二维码类型的支付通道中有效0默认值,返回二维码JSON，商户自行展示1由我公司展示二维码 ,非二维码通道固定为0"
		}
		
		$data['is_sdk'] = '0'; //是否SDK请求
		$data['ts']     = time(); //时间戳
		$data['ext']    = 'ext'; //附加信息
		ksort($data);
		//签名原文
		$signStr = "";
		foreach ($data as $key => $value) {
			if (isset($value) && !is_null($value) && trim($value) != '') {
				$signStr .= $key . '=' . $value . '&';
			}
		}
		$signStr .= "key=" . $payConf['md5_private_key'];
		$data['sign']     = strtoupper(md5($signStr)); //参数签
		unset($reqData);
		return $data;
	}
	
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->getContent();
		$data                = json_decode($arr, true);
		$res['order_id']  = $data['order_id'];
		$res['pay_amount']  = $data['pay_amount'];
		return $res;
	}
	
	public static function SignOther($type, $data, $payConf)
	{
		$sign    = $data['sign'];
		unset($data['sign']);
		ksort($data);
		//签名原文
		$signStr = "";
		foreach ($data as $key => $value) {
			if (isset($value) && !is_null($value) && trim($value) != '') {
				$signStr .= $key . '=' . $value . '&';
			}
		}
		$signStr .= "key=" . $payConf['md5_private_key'];
		$Rsign = strtoupper(md5($signStr)); //参数签名
		if (strtolower($sign) == strtolower($Rsign) && $data['code'] == '0' && $data['status'] == '1') {
			return true;
		} else {
			return false;
		}
	}
	
	
}