<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bfourpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        $data['merchantId'] = $payConf['business_num'];
        $data['timestamp']  = self::getMillisecond();
        $data['tradeNo']    = $order;
        $data['notifyUrl']  = $ServerUrl;
        $data['amount']     = $amount;
        $data['payWay']     = $bank;
        $data['status']     = '1000';
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $key . '=' . urlencode($value) . '&';
        }
        $signStr             = rtrim($signStr, '&');
        $data['wxUserId']    = self::$UserName;
        $data['signature']   = hash_hmac("sha1", $signStr, $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '1' && $data['data']['success']) {
            if(self::$isAPP){
                $data['payUrl'] = $data['data']['codeUrl'];
            }else{
                $data['qrCode'] = $data['data']['codeUrl'];
            }
        }else{
            if(!$data['msg']){
                $data['msg'] = $data['data']['msg'];
            }
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        $body      = stripslashes($arr['body']);
        $data      = json_decode($body, true);
        return $data;
    }

    public static function SignOther($type, $postData, $payConf)
    {
        $body      = stripslashes($postData['body']);
        $data      = json_decode($body, true);
        $signature = $data['signature'];
        unset($data['signature']);
        if ($data['wxUserId']) {
            unset($data['wxUserId']);
        }
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $key . '=' . urlencode($value) . '&';
        }
        $signStr = rtrim($signStr, '&');
        $signTrue    = hash_hmac("sha1", $signStr, $payConf['md5_private_key']);
        if (strtoupper($signature) == strtoupper($signTrue)  && ($data['tradeState'] == "1" || $data['tradeState'] == "2")) {
            return true;
        }
        return false;
    }

    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}