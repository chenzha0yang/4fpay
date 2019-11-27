<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Huanshangpay extends ApiModel
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

		//TODO: do something
		self::$reqType = 'curl';
		self::$payWay  = $payConf['pay_way'];
		self::$unit  = 2;
		self::$resType = 'other';
		self::$postType = true;


		$data['request_id'] = date("YmdHis").rand(100000,999999);
		$data['merchant_no'] = $payConf['business_num'];
		$data['payment'] = $bank;
		$data['total_fee'] = $amount*100;
		$data['order_ip'] = self::getIp();
		$data['out_order_number'] = $order;
		$data['order_title'] = 'ceshi';
		$data['order_desc'] = 'ceshi1';
		$data['notify_url'] = $ServerUrl;
		$data['return_url'] = $returnUrl;

		if ($payConf['pay_way'] == 1) {//网银
			$data['bank_code'] = 401;
		}
		$signStr      = self::getSignStr($data, false,true);
		$data['sign'] = (self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));

		unset($reqData);
		return $data;
	}


	public static function getRequestByType( $data )
	{
		return json_encode($data);
	}


	/**
	 * @param $response
	 * @return mixed
	 */
	public static function getQrCode($response)
	{
		$result = json_decode($response, true);
		if($result['status'] == '000000'){
			if (preg_match("/^(http:\/\/|https:\/\/).*$/", $result['content']) != 0) {//http开头
				$res['qrcode'] = $result['content'];
			} else {
				echo $result['content'];exit;
			}
		} else {
			$res['status'] = $result['status'];
			$res['msg']    = $result['msg'];
		}
		return $res;
	}

	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->all();
		$data['total_fee'] = $arr['total_fee'] / 100;
		$data['out_order_number'] = $arr['out_order_number'];
		return $data;
	}

	/**
	 * @param $type
	 * @param $data
	 * @param $payConf
	 * @return bool
	 */
	public static function SignOther($type, $data, $payConf)
	{
		$signResp=$data['sign'];
		unset($data['sign']);
		$signStr = self::getSignStr($data,false,true);//排序拼接
		$sign = md5($signStr . "&key=" . $payConf['md5_private_key']);
		if (strtolower($sign) == strtolower($signResp) && $data['status'] == 'SUCCESS') {
			return true;
		} else {
			return false;
		}
	}
}