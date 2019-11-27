<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hengrunpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        //$returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
	
	    //判断是否需要跳转链接 is_app=1开启 2-关闭
	    if ($payConf['is_app'] == 1) {
		    self::$isAPP = true;
	    }
        

        $data = array(
            'appID'         => $payConf['business_num'],   //商户号
            'tradeCode'     => $bank,
            'randomNo'      => self::randStr(14),
            'outTradeNo'    => $order,
            'totalAmount'   => $amount * 100,
            'productTitle'  => 'VIP',
            'notifyUrl'     => $ServerUrl,
            'tradeIP'       => self::getIp(),//本地测试IP：192.168.2.50,不能使用127.0.0.1
        );
        ksort($data);
        $signStr      = '';
        foreach ($data as $k =>$v){
            $signStr .= $v.'|';
        }
        $data['sign'] = strtoupper(md5($signStr.$payConf['md5_private_key'])); //MD5签名
        $json = json_encode($data);
        $post = [];
        $post['ApplyParams'] = $json;
        $post['outTradeNo'] = $data['outTradeNo'];
        $post['totalAmount'] = $data['totalAmount'];

        unset($reqData);
        return $post;
    }
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->all();
		$res =  json_decode($arr['NoticeParams'],true);
		$data['totalAmount'] = $res['totalAmount'] / 100;
		$data['outTradeNo'] = $res['outTradeNo'];
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
		$data = json_decode($json['NoticeParams'], true);
		$sign      = $data['sign']; //取SIGN
		unset($data['sign']);
		ksort($data);
		$signStr      = '';
		foreach ($data as $k =>$v){
			$signStr .= $v.'|';
		}
		$mySign = strtoupper(md5($signStr.$payConf['md5_private_key'])); //MD5签
		if ($mySign == strtoupper($sign) && $data['payCode'] == '0000') {
			return true;
		} else {
			return false;
		}
	}
 
 
}