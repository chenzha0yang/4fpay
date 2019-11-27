<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Shenghuipay extends ApiModel
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
		$ServerUrl = $reqData['ServerUrl']; // 异步通知地址
		$returnUrl = $reqData['returnUrl']; // 同步通知地址

		//判断是否需要跳转链接 is_app=1开启 2-关闭
		if ( $payConf['is_app'] == 1 ) {
			self::$isAPP = true;
		}

		//TODO: do something
		self::$method = 'get';

		$data = [
			'machid' => $payConf['business_num'],
			'type' => $bank,
			'amount' => sprintf( '%.2f', $amount ),
			'orderid' => $order,
			'callbackurl' => $ServerUrl,
			'text' => 'goods',
			'hrefbackurl' => $returnUrl
		];

		$signStr = "machid=" . $data['machid'] . "&type=" . $data['type'] . "&amount=" . $data['amount'] . "&orderid=" . $data['orderid'] .
			"&callbackurl=" . $data['callbackurl'];
		$data['sign'] = ( md5( $signStr . $payConf['md5_private_key'] ) );

		unset( $reqData );
		return $data;
	}

	public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data =  json_decode($arr,true);
        return $data;
    }

	/**
	 * @param $type
	 * @param $res
	 * @param $payConf
	 * @return bool
	 */
	public static function SignOther( $type, $res, $payConf )
	{
		$data = file_get_contents("php://input");
		$data=json_decode($data,true);
		$data['amount']=str_replace("_",".",$data['amount']);
		$signTrue = MD5("orderid=".$data['orderid']."&opstate=".$data['opstate']."&amount=".$data['amount'].$payConf['md5_private_key']);

		if ( strtoupper( $data['sign'] ) == strtoupper($signTrue)) {
			return true;
		} else {
			return false;
		}
	}
}