<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Yinshengzpay extends ApiModel
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
	public static function getAllInfo($reqData, $payconf)
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
		if ($payconf['is_app'] == 1) {
			self::$isAPP = true;
		}

		//TODO: do something
		self::$reqType = 'curl';
		self::$payWay  = $payconf['pay_way'];
		self::$httpBuildQuery  = true;
		self::$resType = 'other';


		$data['uid'] = $payconf['business_num'];
		$data['qr_amount'] = $amount;
		$data['notify_url'] = $ServerUrl;
		$data['return_url'] = $returnUrl;
		$data['order_number'] = $order;
		$data['order_uid'] = $order;
		$data['type'] = $bank;
		$sign = $data['notify_url'].$data['order_number'].$data['order_uid'].$data['qr_amount'].$data['return_url'].$data['type'].$data['uid'].$payconf['md5_private_key'];
		$data['key'] = strtoupper(md5($sign));

		unset($reqData);
		return $data;
	}

	/**
	 * @param $response
	 * @return mixed
	 */
	public static function getQrCode($response)
	{
		$result = json_decode($response, true);
		if ($result['retCode'] == '200') {
			$result['qrcode'] = $result['data']['qr_code'];
		}
		return $result;
	}

	/**
	 * @param $type
	 * @param $data
	 * @param $payConf
	 * @return bool
	 */
	public static function SignOther($type, $data, $payConf)
	{
		$sign = strtoupper(md5($data['amount'].$data['order_number'].$payConf['md5_private_key']));
		if( $sign == strtoupper($data['key']) ) {
			return true;
		} else {
			return false;
		}
	}
}