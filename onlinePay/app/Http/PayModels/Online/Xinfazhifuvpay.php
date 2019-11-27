<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Xinfazhifuvpay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType
	
	public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query
	
	public static $isAPP = false; // 判断是否跳转APP 默认false
	
	public static $payCode = [];
	
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
		
		//TODO: do something
		self::$reqType = 'curl';
		self::$payWay  = $payConf['pay_way'];
		self::$method  = 'header';
		self::$resType = 'other';
		self::$unit = 2;
		self::$payCode = $bank;
		
		$data['mchtName'] = (string)'0001';
		$data['txnCode'] = (string)'1001';
		$data['instituId']      = (string)$payConf['business_num']; //机构号
		$data['mchtId'] = (string)'2345667';//商户id
		$data['preOrderId']       = (string)$order; //订单号
		$data['txnDate'] = (string)date('Ymd');
		$data['txnTime'] = (string)date('His');
		$data['terminalIp'] = (string)self::getIp();
		$data['txnType'] = (string)$bank;
		$data['txnAmt']       = (string)($amount * 100); //订单金额 元
		$data['asynUrl'] = (string)$ServerUrl; //异步回调地址
		$signStr = self::getSignStr($data, true,true);
		$data['sign'] = (string)strtoupper(md5($signStr . $payConf['md5_private_key']));
		$post['order']       = $order;
		$post['amount']      = $amount;
		$post['data']        = json_encode($data);
		$post['httpHeaders'] = [
			"Content-Type: application/json; charset=utf-8"
		];
		unset($reqData);
		return $post;
	}
	
	/**
	 * @param $response
	 * @return mixed
	 */
	public static function getQrCode($response)
	{
		$result = json_decode($response, true);
		if ($result['respCode'] == '01') {
			if (in_array((string)self::$payCode, ['3','6','9','21','24'])) {//wap
				$res['payurl'] = $result['qrCode'];
			}
			if (in_array((string)self::$payCode, ['2','5','8','20','23'])) {//h5
				echo $result['qrCode'];exit;
			}
			$res['qrcode'] = $result['qrCode'];
		} else {
			$res['code'] = $result['respCode'];
			$res['msg'] = $result['respMsg'];
		}
		return $res;
		
	}
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->getContent();
		$res =  json_decode($arr,true);
		$data['txnAmt'] = $res['txnAmt'] / 100;
		$data['preOrderId'] = $res['preOrderId'];
		return $data;
	}
	
	
	
	/**
	 * @param $type
	 * @param $json
	 * @param $payConf
	 * @return bool
	 */
	public static function SignOther($type, $json, $payConf)
	{
		$res    = file_get_contents('php://input');
		$data = json_decode($res, true);
		$signA = $data['sign'];
		unset($data['sign']);
		$signStr = self::getSignStr($data, true,true);
		$sign = (string)strtoupper(md5($signStr . $payConf['md5_private_key']));
		if ($sign == $signA && $data['preRespCode'] == '00') {
			return true;
		} else {
			return false;
		}
	}
}