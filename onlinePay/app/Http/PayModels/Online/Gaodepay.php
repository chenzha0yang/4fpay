<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;

class Gaodepay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signData = [];

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$postType = true;
        self::$unit     = 2;
        self::$isAPP    = true;

        $data                 = $datas = $post = [];
        $data['version']      = 'V1.0';
        $data['charset']      = 'UTF-8';
        $data['merchantCode'] = $payConf['business_num'];
        $data['payWay']       = $bank;
        $data['nonceStr']     = (string)(rand());
        $data['orderNo']      = $order;
        $data['totalAmount']  = (string)($amount * 100);
        $data['body']         = 'gd';
        $data['notifyUrl']    = $ServerUrl;
        $data['pageUrl']      = $returnUrl;
        ksort($data);
        $json                =   json_encode($data, 320);
        $data['signature'] = strtoupper(md5($json . $payConf['md5_private_key']));
        $param               = 'reqParam=' . urlencode(json_encode($data, 320)) . '&merchantCode=' . $data['merchantCode'] . '&version=' . $data['version'];
        $post['data']        = $param;
        $post['orderNo']     = $data['orderNo'];
        $post['totalAmount'] = $data['totalAmount'];
        unset($reqData);
        return $post;
    }

    /***
     * @param $post
     * @return mixed
     */
    public static function getRequestByType($post)
    {
        return $post['data'];
    }

    public static function getVerifyResult($res)
    {
        $result              = $res->all();
        
        $data = json_decode($result['reqParam'], true);
        
        $post['totalAmount'] = $data['totalAmount'] / 100;
        $post['orderNo']     = $result['orderNo'];
        return $post;

    }

    public static function SignOther($type, $datas, $payConf)
    {
        $data = json_decode($datas['reqParam'], true);
        $sign = $data['signature'];
        unset($data['signature']);
        ksort($data);
        $md5 = strtoupper(md5(json_encode($data, 320) . $payConf['md5_private_key']));
        if ($md5 == strtoupper($sign)) {
            return true;
        } else {
            return false;
        }
    }
}