<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xingfpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['mchno']      = $payConf['business_num'];//商户号
        $data['pay_type']   = $bank;//支付方式
        $data['price']      = $amount * 100;//金额 分
        $data['bill_title'] = 'zhif';//商品标题
        $data['bill_body']  = 'zhif';//商品描述
        $data['nonce_str']  = self::getRandom(32);//随机数
        $data['linkId']     = $order;//订单号
        $data['notify_url'] = $ServerUrl;
        $data['ip']         = self::getIp();
        ksort($data);
        $str = [];
        foreach ($data as $k => $v) {
            $str[] = "$k=$v";
        }
        $str                 = implode("&", $str) . "&key=" . $payConf['md5_private_key'];
        $data['sign']                = strtoupper(MD5($str));

        $post = json_encode($data);

        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['resultCode'] == '200') {
            $data['qrCode'] = $data['order']['pay_link'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['bill_fee'])) {
            $arr['bill_fee'] = $arr['bill_fee'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signTrue = strtoupper(md5('bill_no=' . $data['bill_no'] . '&bill_fee=' . $data['bill_fee'] . '&key=' . $payconf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue) && $data['feeResult'] == '0') {
            return true;
        }
        return false;
    }

    public static function getRandom($param)
    {
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $key = "";
        for ($i = 0; $i < $param; $i++) {
            $key .= $str{mt_rand(0, 32)};    //生成php随机数
        }
        return $key;
    }

}