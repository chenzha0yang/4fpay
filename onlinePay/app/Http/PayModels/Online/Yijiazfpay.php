<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Yijiazfpay extends ApiModel
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
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data                 = [];
        $data['merchant']     = $payConf['business_num'];
        $data['amount']       = $amount * 100;
        $data['pay_type']      = $bank;
        $data['order_no']     = $order;
        $data['order_time']   = time();
        $data['subject']      = 'goodsName';
        $data['notify_url']   = $ServerUrl;
        $data['callback_url'] = $returnUrl;


        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = sha1($signStr . '&key=' . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function getQrCode($res)
    {
        $arr  = json_decode($res, true);
        $data = [];
        if ($arr['code'] == '0000') {
            $data['qrCode'] = $arr['result']['qrCode'];
        } else {
            $data['code'] = $arr['code'];
            $data['msg']  = $arr['msg'];
        }
        return $data;
    }
    public static function getVerifyResult(Request $request, $mod)
    {
        $data = $request->all();
        if (!isset($data['orderId']) && @empty($data['orderId'])) {
            $json = $request->getContent();
            $data = json_decode($json, true);
        }
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr      = self::getSignStr($data, true, true);
        $mySign = strtoupper(sha1($signStr . '&key=' . $payConf['md5_private_key']));
        if ($sign == $mySign) {
            return true;
        } else {
            return false;
        }
    }
}