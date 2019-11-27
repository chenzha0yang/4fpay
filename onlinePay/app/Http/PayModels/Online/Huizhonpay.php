<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huizhonpay extends ApiModel
{
	public static $method = 'post'; //提交方式 必加属性 post or get
	
	public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet
	
	public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5
	
	public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str
	
	public static $unit = 1; //金额单位  默认1为元  2:单位为分
	
	public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组
	
	public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query
	
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
		$ServerUrl = $reqData['ServerUrl']; // 异步通知地址
		$returnUrl = $reqData['returnUrl']; // 同步通知地址
		
		self::$method = 'get';
		self::$reqType = 'curl';
		self::$resType = 'other';
		self::$payWay  = $payConf['pay_way'];
		self::$unit    = 2;
		//TODO: do something
		$data = array(
			'mch_id'        => $payConf['business_num'],        //商户号
			'out_trade_no'  => $order,                          //订单号
			'body'          => 'VIP',
			'back_url'  => $returnUrl,
			'notify_url'    => $ServerUrl,                      //异步通知地址
			'amount'     => $amount * 100, //金额
			'trade_type'           => $bank,
			'mch_create_ip' => self::getIP(),
			'attach' => 'goods',
		);
		
		//判断是否需要跳转链接 is_app=1开启 2-关闭
		if ($payConf['is_app'] == 1) {
			self::$isAPP        = true;
		}
		$signStr = self::getSignStr($data, true, true);
		$data['sign'] = self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']);
		
		unset($reqData);
		return $data;
	}
	
	public static function getQrCode($response)
	{
		$data = json_decode($response, true);
		if ($data['respCode'] == '00000') {
			$res['qrCode'] = $data['payUrl'];
		} else {
			$res['respMsg'] = $data['respMsg'];
			$res['respCode'] = $data['respCode'];
		}
		return $res;
	}
	
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->all();
		$arr['amount'] = $arr['amount'] / 100;
		return $arr;
	}
	
	public static function SignOther($type, $data, $payConf)
	{
		$sign = $data['sign'];
		$res = [
			'amount' => $data['amount'],
			'notifytime' => $data['notifytime'],
			'out_trade_no' => $data['out_trade_no'],
			'sysNo' => $data['sysNo'],
		];
		$signStr  = self::getSignStr($res, true, true);
		$signTrue = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
		if (strtoupper($sign) == strtoupper($signTrue) && $data['respCode'] == '00000') {
			return true;
		}
		return false;
	}
	
}