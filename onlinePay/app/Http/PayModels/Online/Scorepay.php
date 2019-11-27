<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Scorepay extends ApiModel
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
     * @internal param null|string $user
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

        if($payConf['pay_way'] == '1'){
			$payMethod = "bankPay";
		}else{
			$payMethod = $bank;
		}
		if($payMethod == "bankPay"){
			 // 构造要请求的参数数组
			$parameter = array(
				"service"      => "directPay", 
				"inputCharset" => 'UTF-8', 
				"merchantId"   => $payConf['business_num'],//商户 ID;
				"payMethod"    => $payMethod, 
				"outOrderId"   => $order, 
				"subject"      => "chongzhi", 
				"body"         => 'body',
				"transAmt"     => $amount, 
				"notifyUrl"    => $ServerUrl, 
				"returnUrl"    => $returnUrl, 
				"defaultBank"  => $bank,
				"channel"      => "B2C",
				"cardAttr"     => "01",
			);
            self::$method = 'get';
		}else{ // 扫码
			$parameter = array(
				"service"      => "directPay", 
				"inputCharset" => 'UTF-8', 
				"merchantId"   => $payConf['business_num'],//商户 ID;
				"outOrderId"   => $order, 
				"transAmt"     => $amount,
				"subject"      => "chongzhi",
				"body"         => 'body',
				"payMethod"    => $payMethod,
				"ip"           => $_SERVER["REMOTE_ADDR"],
				"notifyUrl"    => $ServerUrl, 
				"returnUrl"    => $returnUrl, 
				"params"       => "chongzhi",
			);
            self::$payWay  = $payConf['pay_way'];
            self::$reqType = 'fileGet'; // 扫码
            self::$resType = 'other';
		}
        $signStr = self::getSignStr($parameter, true, true);
        $sign    = self::getRsaSign($signStr, $payConf['rsa_private_key']);
        $parameter['sign'] = base64_encode($sign);
        $parameter["signType"] = "RSA";
        unset($reqData);
        return $parameter;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $res = Curl::xmlToArray($response);
        if(isset($res['message']) && !empty($res['message'])){
            $res['message'] = urldecode($res['message']);
        }
        return $res;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        reset($data);

        $signStr = self::getSignStr($data, true, true); //true1 为空不参与签名，true2排序
        $signTrue = self::getRsaSign($signStr, $payConf['public_key']);
        if (strtoupper($signTrue) == strtoupper($sign)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $res['amount'] = $request->transAmt;  //金额
        $res['order'] = $request->outOrderId; //订单号
        return $res;
    }
}