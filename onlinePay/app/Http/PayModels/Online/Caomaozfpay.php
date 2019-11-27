<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Caomaozfpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
	    self::$unit = 2;
	    
        $data['merid'] = $payConf['business_num'];//商户号
        $data['action'] = $bank;//银行编码
        $data['orderid'] = abs($order);//订单号
        $data['txnamt'] = (string)($amount * 100);//订单金额
        $data['backurl'] = $ServerUrl;
        if($bank == 'WxH5'){
            $data['ip'] = self::getIp();
        }
        $arrayJson   = json_encode($data);
        $sign        = md5(base64_encode($arrayJson).$payConf['md5_private_key']);
        
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';
        self::$resType = 'other';
        $requestData = "req=" . urlencode(base64_encode($arrayJson)) . "&sign=" . $sign;
        $datas['data'] = $requestData;
        $datas['httpHeaders'] = array();
        $datas['orderid'] = $data['orderid'];
        $datas['txnamt'] = $data['txnamt'];
        unset($reqData);
        return $datas;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result   = json_decode($response, true);
        $resp     = base64_decode($result['resp']);
        $res      = json_decode($resp, true);
	    if($res['respcode'] == '00'){
		    $aa['qrcode'] = $res['formaction'];
	    } else {
		    $aa['respcode'] = $res['respcode'];
		    $aa['respmsg'] = $res['respmsg'];
	    }
	    return $aa;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $resp = base64_decode($data['resp']);
        $res  = json_decode($resp, true);
        $aa['txnamt'] = $res['txnamt'] / 100;
        $aa['orderid'] = $res['orderid'];
        return $aa;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $resp = base64_decode($data['resp']);
        $res  = json_decode($resp, true);
        $sign      = $data['sign'];
        $Mysign    = md5($data['resp'].$payConf['md5_private_key']);
        if(strtolower($sign) == strtolower($Mysign)){
            return true;
        } else {
            return false;
        }
    }
}