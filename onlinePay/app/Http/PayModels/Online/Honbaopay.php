<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Honbaopay extends ApiModel
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
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';
        self::$resType = 'other';
        self::$unit    = 2;


        $data['shop_no']        = $payConf['business_num'];
        $data['trans_type_code']      = $bank;
        $data['money']         = $amount * 100;
        $data['shop_order_no'] = $order;
        $data['shop_order_time'] = date('Y-m-d H:i:s');
        $data['order_remark'] = 'goods';
        $data['order_name'] = 'goods';

        if ($payConf['pay_way'] == 2) {
            $data['pay_resource'] = 1;
        } else if ($payConf['pay_way'] == 3) {
            $data['pay_resource'] = 2;
        } else {
            $data['pay_resource'] = 0;
        }

        $data['timestamp'] = time();
        $data['callback_url']    = $ServerUrl;

        $signStr = '';
        ksort($data);

        foreach ($data as $k => $v) {
            $signStr .= $k . $v;
        }
        $data['sign']          = sha1($signStr . $payConf['md5_private_key']);

        $post['order']       = $order;
        $post['amount']      = $data['money'];
        $post['data']        = json_encode($data);
        $post['httpHeaders'] = ["Content-Type: application/json; charset=utf-8"];

        unset($reqData);
        return $post;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['code'] == 'R0001') {
            $result['qrcode'] = $result['data']['qrCodeUrl'];
        }
        return $result;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->all();
        $arr['payMoney'] = $arr['payMoney'] / 100;
        return $arr;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr = '';
        ksort($data);
        foreach ($data as $k => $v) {
            $signStr .= $k . $v;
        }

        $signTrue = sha1($signStr . $payConf['md5_private_key']);
        if (strtolower($sign) == strtolower($signTrue) && $data['orderStatus'] == 1) {
            return true;
        } else {
            return false;
        }
    }
}