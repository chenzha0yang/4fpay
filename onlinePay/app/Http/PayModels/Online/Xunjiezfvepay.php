<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xunjiezfvepay extends ApiModel
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
        self::$unit     = 2; // 单位 ： 分
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$resType  = 'other';

        $data         = array(
            'action'   => 'USao',
            'txnamt'   => $amount * 100,
            'merid'    => $payConf['business_num'],
            'orderid'  => $order,
            'backurl'  => $ServerUrl,
//            'fronturl' => $returnUrl,
        );
        $post['req']          = base64_encode(json_encode($data));
        $post['sign'] = md5(base64_encode(json_encode($data)) . $payConf['md5_private_key']);
        $post['orderid'] = $data['orderid'];
        $post['txnamt'] = $data['txnamt'];
        unset($reqData);
        return $post;
    }

    public static function getQrcode($req)
    {
        // Json转数组
        $reqArr = json_decode($req, true);
        // 取出数组中base64解密
        $respJson = base64_decode($reqArr['resp']);
        //解密后数据数组
        $data = json_decode($respJson, true);
        return $data;
    }

    //回调金额处理
    public static function getVerifyResult($request)
    {
        $data                = $request->all();
        $req          = json_decode(base64_encode($data['resp']),true);
        $data['txnamt']   = $req['txnamt'] / 100;
        $data['orderid'] = $req['orderid'];
        return $data;
    }

    //回调特殊处理
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $req          = json_decode(base64_encode($data['resp']),true);
        $signTrue    = md5($data['resp'].$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $req["resultcode"] == "0000") {
            return true;
        } else {
            return false;
        }
    }
}