<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Yixingpay extends ApiModel
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
    public static function getAllInfo($reqData, $payConf)
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
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        $data['appid'] = $payConf['business_num'];//商户号
        $data['pay_id'] = 'WX_RANDOM';//银行编码
        $data['out_trade_no'] = $order;//订单号
        $data['version'] = '1.0';
        $data['total_fee'] = sprintf('%.2f', $amount);//订单金额
        $data['sign_type'] = 'MD5';
        $data['notify_url'] = $ServerUrl;
        $data['currency_type'] = 'CNY';
        $data['return_url'] = $returnUrl;
        $data['order_type'] = '1';
        $data['user_id'] = $payConf['business_num'];
        $data['pay_ip']  = self::getIp();
        $data['goods_name'] = base64_encode('goods');

        if (self::$payWay == 6) {$data['bank_code'] = $bank;}

        $signStr = "appid={$data['appid']}&currency_type={$data['currency_type']}&goods_name={$data['goods_name']}&order_type={$data['order_type']}&out_trade_no={$data['out_trade_no']}&pay_id={$data['pay_id']}&return_url={$data['return_url']}&sign_type={$data['sign_type']}&total_fee={$data['total_fee']}&version={$data['version']}";
        $data['sign'] = (md5($signStr . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    //回调金额化分为元

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->getContent();
        $arr = json_decode($arr, true);
        $data['amount'] = $arr['amount'];
        $data['out_trade_no'] = $arr['out_trade_no'];
        return $data;
    }

	/**
	 * @param $type
	 * @param $res
	 * @param $payConf
	 * @return bool
	 */
    public static function SignOther($type, $res, $payConf)
    {
    	$data = file_get_contents('php://input');
    	$data = json_decode($data, true);

        $sign = $data['sign'];

	    $signStr = "amount={$data['amount']}&appid={$payConf['business_num']}&currency_type={$data['currency_type']}&goods_name={$data['goods_name']}&out_trade_no={$data['out_trade_no']}&pay_id={$data['pay_id']}&pay_no={$data['pay_no']}&payment={$data['payment']}&resp_code={$data['resp_code']}&resp_desc={$data['resp_desc']}&sign_type={$data['sign_type']}&tran_amount={$data['tran_amount']}&version={$data['version']}";
	    $signTrue = strtoupper((md5($signStr . $payConf['md5_private_key'])));
        if (strtoupper($sign) == $signTrue && $data['resp_code'] == '00') {
            return true;
        } else {
            return false;
        }
    }
}