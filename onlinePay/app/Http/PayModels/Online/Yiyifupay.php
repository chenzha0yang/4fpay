<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yiyifupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 2; //金额单位  默认1为元  2:单位为分

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
	    self::$reqType = 'curl';
	    self::$payWay  = $payConf['pay_way'];
	    self::$resType    = 'other';
	    self::$httpBuildQuery = true;
	    self::$unit   = 2;
	    
        $data = [];
        $data['mchid']    = $payConf['business_num'];          //商户号
        $data['src_code'] = $payConf['public_key'];       //秘钥
        $data['out_trade_no'] = $order;                          //订单号
        $data['total_fee']  = $amount * 100;                          //金额
        $data['trade_type'] = $bank;                           //支付类型
        $data['goods_name'] = 'test';
        $data['time_start'] = date('YmdHis');

        $signStr = self::getSignStr($data, false);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $data['finish_url'] =$ServerUrl;                     //异步通知URL
        $data['returnUrl'] = $returnUrl;                    //同步跳转URL
        unset($reqData);
        return $data;
    }
	
	public static function getQrCode($response)
	{
		$data = json_decode($response,true);
		if ($data['respcd'] == '0000') {
			$res['code_url'] = $data['data']['pay_params'];
		}else{
			$res['msg'] = $data['respmsg'];
			$res['code'] = $data['respcd'];
		}
		return $res;
	}
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->all();
		$data['total_fee'] = $arr['total_fee'] / 100;
		$data['out_trade_no'] = $arr['out_trade_no'];
		return $data;
	}
	
	
	public static function SignOther($type, $data, $payConf)
	{
		$datasign = $data['sign'];
		unset($data['sign']);
		$signStr = self::getSignStr($data, false);
		$sign = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
		if ($sign == strtoupper($datasign) && $data['order_status'] == '3') {
			return true;
		} else {
			return false;
		}
	}
	
}