<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Saicpay extends ApiModel
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
	public static function getAllInfo( $reqData, $payConf )
	{
		/**
		 * 参数赋值，方法间传递数组
		 */
		$order = $reqData['order'];
		$amount = $reqData['amount'];
		$bank = $reqData['bank'];
		$ServerUrl = $reqData['ServerUrl'];// 异步通知地址
		$returnUrl = $reqData['returnUrl'];// 同步通知地址

		//TODO: do something

		self::$reqType = 'curl';
		self::$payWay = $payConf['pay_way'];
		self::$postType = true;
		self::$method = 'header';
		self::$resType = 'other';
		self::$isAPP = true;

		$data['paytype'] = $bank; //类型请自行调整
		$data['out_trade_no'] = $order; //平台订单
		$data['notify_url'] = $ServerUrl; //这个是订单回调地址，成功付款后定时通知队列会调这个地址。
		$data['return_url'] = $returnUrl; //这个是订单回调地址，成功付款后实时跳回这个地址。
		$data['goodsname'] = "cszf"; //商品名称
		$data['total_fee'] = $amount; //定单金额，不要带小数，必须是整数
		$data['remark'] = "wjsys"; //平台的名称，做区分用的。
		$data['requestip'] = self::getIp(); //玩家的IP。

		$strs = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm"; //随机数基本字符串
		$data1['mchid'] = (int)$payConf['business_num']; //商户ID，请自行调整
		$data1['timestamp'] = time(); //时间戳
		$data1['nonce'] = substr( str_shuffle( $strs ), mt_rand( 0, strlen( $strs ) - 11 ), 10 );
		$data1['sign'] = md5( self::getSignStr( array_merge( $data, $data1 ), true, true ) . "&key=" . $payConf['md5_private_key'] );//商户密匙，请自行调整
		$data1['data'] = $data;
		$post['order'] = $order;
		$post['amount'] = $amount;
		$post['data'] = json_encode( $data1 );
		$post['httpHeaders'] = [
			'Content-Type: application/json',
			'Content-Length: ' . strlen( json_encode( $data1 ) )
		];
		unset( $reqData );
		return $post;
	}

	//处理提交数据
	public static function getRequestByType( $request )
	{
		return $request['data'];
	}

	//返回html页面
	public static function getQrCode( $response )
	{
		$result = json_decode( $response, true );
		if ($result['error'] == '0') {
			$res['payUrl'] = $result['data']['payurl'];
		} else {
			$res['code'] = $result['error'];
			$res['msg'] = $result['msg'];
		}
		return $res;
	}

	//回调金额化分为元
	public static function getVerifyResult(Request $request, $mod )
	{
		$result = $request->getContent();
		$post = json_decode($result, true);
		return $post;
	}

	public static function SignOther( $model, $dataS, $payConf )
	{
		$data = file_get_contents("php://input");
		$post = json_decode($data, true);
		$sign    = $post['sign'];
		unset($post['sign']);
		$getsign = md5(self::getSignStr($post, true, true) . "&key=" . $payConf['md5_private_key']); //商户的KEY，请自行置换
		if (strtolower($getsign) == strtolower($sign)) {
			return true;
		}
		return false;
	}
}


