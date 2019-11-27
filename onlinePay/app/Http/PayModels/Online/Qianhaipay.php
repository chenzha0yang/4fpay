<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Qianhaipay extends ApiModel
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
		self::$method = 'header';
		self::$resType = 'other';
		self::$isAPP = true;

		$data['version']    = '1.0'; //默认 1.0
        $data['merId']      = $payConf['business_num']; //商户号
        $data['orderId']    = $order; //订单
        $data['totalMoney'] = $amount * 100;
        $data['tradeType']  = $bank; //支付类型
        if ((int)$payConf['pay_way'] === 1) {
            $data['tradeType'] = 'union_gateway'; //支付类型
            $data['bankCode']  = $bank; //银行缩写
        }
        $data['ip']          = self::getIp();
        $data['describe']    = 'vivo'; //商品描述
        $data['notify']      = $ServerUrl;
        $data['redirectUrl'] = $returnUrl;
        $data['remark']      = ''; //随机字符串
        $data['fromtype']    = 'pc';
        if ($payConf['is_app'] == 1) {
            $data['fromtype'] = 'wap';
        }
        $string              = 'merId=' . $data['merId'] . '&orderId=' . $data['orderId'] . '&totalMoney=' . $data['totalMoney'] . '&tradeType=' . $data['tradeType'] . '&' . $payConf['md5_private_key'];
        $data['sign']        = strtoupper(md5($string)); //签名

        $post['orderId'] = $data['orderId'];
        $post['totalMoney'] = $data['totalMoney'];
		$post['data'] = json_encode( $data );
		$post['httpHeaders'] = [
			'Content-Type: application/json',
			'Content-Length: ' . strlen( json_encode( $data ) )
		];
		unset( $reqData );
		return $post;
	}

	//返回html页面
	public static function getQrCode( $response )
	{
		$result = json_decode( $response, true );
		if ($result['code'] == '0') {
			$res['payUrl'] = $result['object']['data'];
		}
		return $res;
	}

	//回调金额化分为元
	public static function getVerifyResult(Request $request, $mod )
	{
		$result = $request->all();
		$result['money'] = $result['money']/100;
		$result['orderId'] = $result['orderId'];
		return $result;
	}

	public static function SignOther( $model, $data, $payConf )
	{
		$getsign = $data['sign'];
		$str  = "code" . $data['code'] . 'merId' . $data['merId'] . 'money' . $data['money'] . 'orderId' . $data['orderId'] . 'payWay' . $data['payWay'] . 'remark' . $data['remark'] . 'time' . $data['time'] . 'tradeId' . $data['tradeId'];
        $sign = strtoupper(md5($str . $payConf['md5_private_key']));
		if (strtoupper($getsign) == strtoupper($sign) && $data['code'] == '0') {
			return true;
		}
		return false;
	}
}


