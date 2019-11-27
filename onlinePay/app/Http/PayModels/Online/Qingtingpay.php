<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qingtingpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $is_app = '';

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

        //TODO: do something

        self::$unit     = 2;
        self::$isAPP    = true;
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$postType = true;
        self::$resType  = 'other';
        if ($payConf['is_app'] == 1) {
            self::$is_app = 1;
        }

        $pt                 = explode("@", $payConf['business_num']);
        $pay_id             = $pt['0']; #商户号
        $app_id             = $pt['1']; #应用ID
        $data['mchId']      = $pay_id; #商户号
        $data['appId']      = $app_id; #应用ID
        $data['productId']  = $bank;
        $data['mchOrderNo'] = $order; #商户订单号
        $data['currency']   = 'cny'; #币种
        $data['amount']     = (string)($amount * 100);
        $data['clientIp']   = '127.0.0.1';
        $data['device']     = 'WEB';
        $data['returnUrl']  = $returnUrl;
        $data['notifyUrl']  = $ServerUrl; #通知地址
        $data['subject']    = "honor"; #商品名称
        $data['body']       = 'i20';
        $data['param1']     = '';
        $data['param2']     = '';
        $data['extra']      = '{"openId":"123456"}';
        $str                = self::getSignStr($data, true, true);
        $data['sign']       = strtoupper(md5($str . '&key=' . $payConf['md5_private_key']));
        $param              = ["params" => json_encode($data)];
        $post['data']       = $param;
        $post['amount']     = $data['amount'];
        $post['mchOrderNo'] = $data['mchOrderNo'];
        unset($reqData);
        return $post;
    }

    public static function getRequestByType($post)
    {
        return $post['data'];
    }

    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);
        if ($result['retCode'] == 'SUCCESS') {
            if (self::$is_app = 1) {
                $result['payUrl'] = $result['payParams']['codeUrl'];
            } else {
                $result['payUrl'] = $result['payParams']['codeImgUrl'];
            }
        }
        return $result;
    }

    public static function getVerifyResult($request)
    {
        $res           = $request->all();
        $res['amount'] = $res['amount'] / 100;
        return $res;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        if ($sign == $mySign && $data['status'] == '2') {
            return true;
        }
        return false;
    }
}