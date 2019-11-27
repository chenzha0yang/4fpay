<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Xinxiezfpay extends ApiModel
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

    public static $imgSrc =false;

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
        self::$isAPP = true;

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        self::$unit    = 2;

        $payInfo = explode('@', $payConf['business_num']);
        $data['mchId'] = $payInfo[0];//商户号
        if(!isset($payInfo[1])){
            echo '商户号请填入格式为：商户号@appId 的商户信息';exit;
        }
        $data['appId'] = $payInfo[1];
        $data['productId'] = (int)$bank;//银行编码
        $data['mchOrderNo'] = $order;//订单号
        $data['currency'] = 'cny';
        $data['amount'] = (int) $amount * 100;//订单金额
        $data['subject'] = 'test';
        $data['notifyUrl'] = $ServerUrl;
        $data['body'] = 'test';
        $data['returnUrl'] = $returnUrl;
        $data['extra'] = 'test';
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['retCode'] == 'SUCCESS') {
            if(isset($result['payParams']['codeImgUrl'])){
                $res['qrCode'] = $result['payParams']['codeImgUrl'];
                self::$isAPP=false;
                self::$imgSrc =true;
            }else{
                $res['qrCode'] = $result['payParams']['payUrl'];
            }
        } else {
            $res['retCode'] = $result['retCode'];
            $res['retMsg'] = $result['retMsg'];
        }
        return $res;
    }

    //回调金额化分为元

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->all();
        $data['amount'] = $arr['amount'] / 100;
        $data['mchOrderNo'] = $arr['mchOrderNo'];
        return $data;
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
        $signStr = self::getSignStr($data, true, true);
        $signTrue = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        if (strtoupper($sign) == $signTrue && $data['status'] == '2') {
            return true;
        } else {
            return false;
        }
    }
}