<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\File;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;
use Illuminate\Http\Request;

class Guanshupay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType
	
	public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query
	
	public static $isAPP = false; // 判断是否跳转APP 默认false

	public static $rsa_private_key = '';
	
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
		
		//判断是否需要跳转链接 is_app=1开启 2-关闭
		if ($payConf['is_app'] == 1) {
			self::$isAPP = true;
		}
		
		//TODO: do something
		self::$reqType = 'curl';
		self::$payWay  = $payConf['pay_way'];
		self::$method = 'header';
		self::$resType = 'other';
		self::$postType = true;
		
		$context               = [];
		$context['product']    = 'goodsName';
		$context['amount']     = sprintf('%.2f', $amount);
		$context['orderNo']    = $order;
		$context['merchNo']    =  $payConf['business_num'];
		$context['memo']       = 'pay:' . $context['amount'];
		$context['notifyUrl']  = $ServerUrl;
		$context['returnUrl']  = $returnUrl;
		$context['currency']   = 'CNY';
		$context['reqTime']    = date('YmdHis');
		$context['title']      = 'title';
		$context['userId']     = (string)time();
		$context['outChannel'] = $bank;//银行编码
		
		if ($payConf['pay_way'] == 1) {
			$context['outChannel'] = 'wy';//银行编码
			$context['bankCode'] = $bank;
		} else {
			$context['bankCode'] = 'qq2';
		}
		$json = json_encode($context,JSON_UNESCAPED_UNICODE);
		$json = stripslashes($json);
		$pu_bopay_key = openssl_pkey_get_public($payConf['public_key']);//这个函数可用来判断平台提供的公钥是否是可用的，可用返回资源id Resource id
		$cryptos = '';
		foreach (str_split($json, 117) as $value) {
			openssl_public_encrypt($value, $encryptDatas,$pu_bopay_key);
			$cryptos .= $encryptDatas;
		}
		$context = base64_encode($cryptos);
		$pi_key = openssl_get_privatekey($payConf['rsa_private_key']);//这个函数可用来判断商户私钥是否是可用的，可用返回资源id Resource id
		
		self::$rsa_private_key = $payConf['rsa_private_key'];
		openssl_sign($cryptos,$sign,$pi_key,OPENSSL_ALGO_MD5);//根据提供的私钥进行订单签名OPENSSL_ALGO_MD5
		$sign=base64_encode($sign);//最终的签名
		$data = array(
			'context' => $context,
			"sign"=>$sign,
		);
		$post['data']        = json_encode($data);
		$post['httpHeaders'] = array(
			'Content-Type: text/html; charset=UTF-8'
		);
		$post['orderNo'] = $order;
		$post['amount']     = sprintf('%.2f', $amount);
		unset($reqData);
		return $post;
	}
	
	public static function getRequestByType($post)
	{
		return $post['data'];
	}
	
	public static function getQrCode($response)
	{
		$data = json_decode($response,true);
		if ($data['code'] == '0') {
			$pi_key = openssl_pkey_get_private(self::$rsa_private_key);//取私钥资源号
			$data = self::PrivateDecrypt($data['context'],$pi_key);//执行解密流程
			$context_arr = json_decode($data,true);//转为数组格
			$res['code_url'] = $context_arr['qrcode_url'];
		}else{
			$res['msg'] = $data['msg'];
			$res['code'] = $data['code'];
		}
		return $res;
	}
	
	//回调金额化分为元
	public static function getVerifyResult(Request $request, $mod)
	{
		$arr = $request->getContent();
		$res =  json_decode($arr,true);
		if(isset($res['context'])){
			$bankOrder = PayOrder::getOrderData($res['orderNo']);//根据订单号 获取入款注单数据
	        if (empty($bankOrder)) {
	            //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
	            File::logResult($request->all());
	            return trans("success.{$mod}");
	        }
	        $payConf = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
			$pi_key = openssl_pkey_get_private($payConf['rsa_private_key']);//取私钥资源号
			$ress = self::PrivateDecrypt($res['context'],$pi_key);//执行解密流程
			$context_arr = json_decode($ress,true);//接收到的返回数据转为数组形式，自行写入数据库
			$result['amount'] = $context_arr['amount'];
			$result['orderNo'] = $context_arr['orderNo'];
		}else{
			$result['amount'] = '';
			$result['orderNo'] = '';
		}
		return $result;
	}
	
	public static function signOther($mod, $datas, $payConf)
	{
		$post    = file_get_contents("php://input");
		$data = json_decode($post,true);
		$pu_bopay_key = openssl_pkey_get_public($payConf['public_key']);
		$flag = (bool)openssl_verify(self::urlsafe_b64decode($data['context']),self::urlsafe_b64decode($data['sign']),$pu_bopay_key,OPENSSL_ALGO_MD5);//验证签名
		$pi_key = openssl_pkey_get_private($payConf['rsa_private_key']);//取私钥资源号
		$res = self::PrivateDecrypt($data['context'],$pi_key);//执行解密流程
		$context_arr = json_decode($res,true);//接收到的返回数据转为数组形式，自行写入数据库
		if ($flag && $context_arr['orderState'] == '1') {
			return true;
		} else {
			return false;
		}
	}
	
	
	//解密流程函数
	public static function PrivateDecrypt($encrypted,$pi_key){
		$crypto = '';
		foreach (str_split(self::urlsafe_b64decode($encrypted), 128) as $chunk) {
			
			openssl_private_decrypt($chunk, $decryptData, $pi_key);
			
			$crypto .= $decryptData;
			
		}
		
		return $crypto;
	}
	//解密函数
	public static function urlsafe_b64decode($string) {
		$data = str_replace(array('-','_'),array('+','/'),$string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}
	
	
}