<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;
use App\Extensions\Curl;

class Skbankpay extends ApiModel
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
	
	public static $changeUrl = false;
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
		
		self::$isAPP = true;
		
		//TODO: do something
		self::$reqType = 'curl';
		self::$payWay  = $payConf['pay_way'];
		self::$httpBuildQuery  = true;
		self::$changeUrl  = true;
		
		$data['appId'] = $payConf['business_num'];//商户号
		$data['payType'] = $bank;//银行编码
		$data['outOrderNo'] = $order;//订单号
		$data['amount'] = $amount;//订单金额
		$data['timestamp'] = static::getMillisecond();
		$data['nonceStr'] = static::getRandomStr(30,false);
		$data['attach'] = 'test';
		$data['signature'] = static::getSign($data['outOrderNo'],$data['amount'],$data['payType'],$data['attach'],$data['appId'],$data['timestamp'],$data['nonceStr'],$payConf['md5_private_key']);
		$data['orderDesc'] = 'test';
		$data['returnUrl'] = $returnUrl;
		$data['notifyUrl'] = $ServerUrl;
		$data['userUnqueNo'] = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
		
		$post['queryUrl'] = $reqData['formUrl'].'/open/otc/create';
		$post['data'] = $data;
		$post['outOrderNo'] = $order;
		$post['amount'] = $amount;
		
		unset($reqData);
		return $post;
	}
	
	
	/**
	 * @param $type
	 * @param $json
	 * @param $payConf
	 * @return bool
	 */
	public static function SignOther($type, $data, $payConf)
	{
		$sign = $data['signature'];
		unset($data['signature']);
		$signTrue = static::getSign($data['outOrderNo'],$data['money'],$data['payType'],$data['attach'],$payConf['business_num'],$data['timestamp'],$data['nonceStr'],$payConf['md5_private_key']);
		if ($sign == $signTrue && $data['status'] == 'SUCCESS') {
			return true;
		} else {
			return false;
		}
	}
	
	
	//获取随机字符串
	static function getRandomStr($len, $special=true){
		$chars = array(
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
			"l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
			"w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
			"H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
			"S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
			"3", "4", "5", "6", "7", "8", "9"
		);
		if($special){
			$chars = array_merge($chars, array(
				"!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
				"%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
				"}", "<", ">", "~", "+", "=", ",", "."
			));
		}
		$charsLen = count($chars) - 1;
		shuffle($chars);                            //打乱数组顺序
		$str = '';
		for($i=0; $i<$len; $i++){
			$str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
		}
		return $str;
	}
	
	public static function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	
	public static function getSign($outOrderNo,$amount,$payType,$attach,$appId,$timestamp,$nonceStr,$secret){
		$params=array("outOrderNo"=>$outOrderNo,"amount"=>$amount,"payType"=>$payType,"attach"=>$attach);
		ksort($params);//把key，从小到大排序
		$paramUrl = "?".http_build_query($params, '' , '&');//封装成url参数
		//print_r($paramUrl);
		$md5Value=strtolower(md5($paramUrl.$appId.$timestamp.$nonceStr));
		return strtoupper(md5($md5Value.$secret));
	}
	
}