<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Shandiantianpay extends ApiModel
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

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$isAPP = true;
        self::$resType  = 'other';


        $data['mch_id']   = $payConf['business_num'];
        $data['service'] = $bank;
        $data['out_trade_no']     = $order;
        $data['trade_time']= date('YmdHis',time());
        $data['attach']='213';
        $data['subject']    = 'ipad';
        $data['body'] ='ipad';
		$data['total_fee']   = number_format($amount, 2, '.', '');
        $data['spbill_create_ip'] = self::getIp();
        $data['notify_url']       = $ServerUrl;
        $data['return_url']       = $returnUrl;
		$data['sign_type']         = 'MD5';
        $data['trade_type']   = 'H5';
		$signStr             = self::getSignStr($data,true, true);
		$data['sign']        =strtoupper( md5($signStr . '&key=' .  $payConf['md5_private_key']));
		unset($reqData);
		return $data;
	}

    /**
     * @param $resp
     * @return mixed
     */
    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);
        if ($result['return_code'] == 'success') {
            $result['mweb_url'] = $result['mweb_url'];
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
		$sign    = $data['sign'];
		unset($data['sign']);
		$signStr      = self::getSignStr($data,true, true);
		$signTrue = md5($signStr . '&key=' . $payConf['md5_private_key']);
		if (strtoupper($sign) == strtoupper($signTrue) && $data['result_code'] == 'success') {
			return true;
		} else {
			return false;
		}
	}
}