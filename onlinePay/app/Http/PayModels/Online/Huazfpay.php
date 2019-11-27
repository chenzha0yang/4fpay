<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huazfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

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

        self::$isAPP = true;
        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        $amount = $amount*100;
        $data['merchantNo'] = $payConf['business_num'];//商户号
        $data['version'] = 'v2.0';
        $data['payMethod'] = $bank;//银行编码
        $data['name'] = 'test';
        $data['orderNo'] = $order;//订单号
        $data['total'] = (int)$amount;//订单金额
        $data['timestamp'] = self::getMillisecond();
        $data['returnUrl'] = $returnUrl;
        $data['notifyUrl'] = $ServerUrl;
        $data['sign'] =  self::get_sign($data,$payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '1') {
            $data['payUrl'] = $data['result']['payUrl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['total'])) {
            $arr['total'] = $arr['total'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signTrue = self::get_sign($data,$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)  && $data['code'] == '1') {
            return true;
        }
        return false;
    }

    //函数,获取毫秒时间戳
    public static function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /*签名*/
    public static function get_sign($obj,$key) {
        $signPars = "";
        ksort($obj);
        foreach ($obj as $k => $v) {
            if ("sign" != $k) {
                $signPars.= $k . "=" . $v;
            }
        }
        $signPars.= $key;
        $my_sign = strtolower(md5($signPars));
        return $my_sign;
    }
}