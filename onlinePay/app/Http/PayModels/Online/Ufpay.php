<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Ufpay extends ApiModel
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
		
		$data['merchantId'] = $payConf['business_num'];
		$data['notifyUrl']  = $ServerUrl;
		$data['outOrderId'] = $order;
		$data['subject']    = 'vivo';
		$data['body']       = 'X20';
		$data['transAmt']   = $amount;
		if ($payConf['pay_way'] != '1') {
			$data['scanType'] = $bank;
		}
		if ($payConf['pay_way'] == '1') {
			//网银
			$data['returnUrl']   = $returnUrl;
			$data['defaultBank'] = $bank;
			$data['channel']     = 'B2C'; //银行渠道，默认为B2C
			$data['cardAttr']    = '1'; //卡类型，默认1为借记卡
		}
		$data['sign'] = self::buildRequestPara($data, $payConf['rsa_private_key']);
		
		
		if (!self::$isAPP && $payConf['pay_way'] != '1') {
			self::$reqType = 'curl';
			self::$payWay  = $payConf['pay_way'];
			self::$httpBuildQuery = true;
		}
		unset($reqData);
		return $data;
	}
	
	/**
	 * @param $type
	 * @param $data
	 * @param $payconf
	 * @return bool
	 */
	public static function SignOther($type, $data, $payconf)
	{
		$sign     = $data["sign"];
		$respType = $data['respType'];
		$verify = self::verifyRSA($data, $sign, $payconf['public_key']);
		if ($verify && $respType == 'S') {
			return true;
		} else {
			return false;
		}
	}
	
	
	public static function buildRequestPara($para_temp, $pri_key)
	{
		//除去待签名参数数组中的空值和签名参数
		$para_filter = self::paraFilter($para_temp);
		//对待签名参数数组排序
		$para_sort = self::argSort($para_filter);
		//生成签名结果
		$mysign = self::buildMysignRSA($para_sort, trim($pri_key));
		//签名结果与签名方式加入请求提交参数组中
		$sign = $mysign;
		return $sign;
	}
	
	/**
	 * 除去数组中的空值和签名参数
	 * @param $para 签名参数组
	 * return 去掉空值与签名参数后的新签名参数组
	 * @return array
	 */
	public static function paraFilter($para)
	{
		$para_filter = array();
		while (list($key, $val) = each($para)) {
			if ($key == "sign" || $key == "signType" || $val == "") {
				continue;
			} else {
				$para_filter[$key] = $para[$key];
			}
			
		}
		return $para_filter;
	}
	
	/**
	 * 对数组排序
	 * @param $para 排序前的数组
	 * return 排序后的数组
	 * @return 排序前的数组
	 */
	public static function argSort($para)
	{
		ksort($para);
		reset($para);
		return $para;
	}
	public static function buildMysignRSA($sort_para, $priKey)
	{
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = self::createLinkstring($sort_para);
		//把最终的字符串签名，获得签名结果
		
		$mysign = self::signRSA($prestr, $priKey);
		return $mysign;
	}
	
	public static function createLinkstring($para)
	{
		$arg = "";
		while (list($key, $val) = each($para)) {
			$arg .= $key . "=" . $val . "&";
		}
		//去掉最后一个&字符
		$arg = substr($arg, 0, count($arg) - 2);
		//如果存在转义字符，那么去掉转义
		$arg = stripslashes($arg);
		return $arg;
	}
	
	public static function signRSA($data, $priKey)
	{
		// 转换为openssl密钥，必须是没有经过pkcs8转换的私钥
		$res = openssl_get_privatekey($priKey);
		// 调用openssl内置签名方法，生成签名$sign
		openssl_sign($data, $sign, $res);
		// 释放资源
		openssl_free_key($res);
		// base64编码
		$sign = base64_encode($sign);
		return $sign;
	}
	
	
	
	
}