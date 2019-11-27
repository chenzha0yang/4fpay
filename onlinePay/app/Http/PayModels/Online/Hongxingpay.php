<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hongxingpay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query
	
	public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组
	
	public static $isAPP = false; // 判断是否跳转APP 默认false
	
	public static $payConf = [];

	public  static  $publicKey = '';
	
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
		
		self::$payConf = $payConf;
		if(!$payConf['public_key']){
		    echo '请将deskey填入公钥栏!';exit();
        }
		self::$publicKey = $payConf['public_key'];

		//判断是否需要跳转链接 is_app=1开启 2-关闭
			self::$isAPP = true;
		$aa = explode('@', $payConf['business_num']);
		if(!isset($aa[1])){
		    echo '格式绑定有误!商户id栏请参照 userid@appid 绑定';exit();
        }
		$data['cmd'] = $bank;
		$data['version'] = '2.0';
		$data['userid']       = $aa[0]; //商户id
		$data['appid'] = $aa[1];
		$data['apporderid']       = $order; //订单号
		$data['ordertime'] = date('YmdHis');
		$data['orderbody'] = 'ceshi';
		$data['amount']       = sprintf('%.2f', $amount); //订单金额 元
		$data['notifyurl'] = $ServerUrl; //异步回调地址
		if($payConf['pay_way'] == 9){
			$data['accno'] = null;
			$data['biztype'] = 1;
			$data['ordertitle'] = 'test';
			$data['pageurl'] = $returnUrl;
		}else{
			if(self::$isAPP){
				$data['front_skip_url'] =$returnUrl;
			}
			$data['custip']   = self::getIp();
		}
		$signStr = self::getSignStr($data,true,true);
		$data['hmac'] = md5($signStr . $payConf['md5_private_key']);

		if ($payConf['pay_way'] != 9 || self::$isAPP) {
			self::$reqType = 'curl';
			self::$payWay  = $payConf['pay_way'];
			self::$resType = 'other';
			self::$httpBuildQuery = true;
		}
		unset($reqData);
		return $data;
	}
	
	
	public static function getQrCode($response)
	{
		//$data = explode('@', self::$payConf['business_num']);
		$res = explode('&', $response);
		$payurl = explode('=', $res[5]);

		if ($res['3'] == 'errcode=3') {
			if (self::$isAPP) $result['payUrl'] = openssl_decrypt(base64_encode(hex2bin($payurl[1])), 'des-ecb', self::$publicKey);
			else $result['qrcode'] = openssl_decrypt(base64_encode(hex2bin($payurl[1])), 'des-ecb', self::$publicKey);
		} else {
			$result['code'] = $res['3'];
			$result['msg'] = $res['1'];
		}
		return $result;
	}
	
	
	public static function SignOther($type, $data,$payConf)
	{
		$signA = $data['hmac'];
		unset($data['hmac']);
		$signStr =  self::getSignStr($data,true,true);
		$sign = strtolower(md5($signStr . $payConf['md5_private_key']));
		if ($sign == strtolower($signA) && $data['status'] == 0) {
			return true;
		} else {
			return false;
		}
	}
	
}