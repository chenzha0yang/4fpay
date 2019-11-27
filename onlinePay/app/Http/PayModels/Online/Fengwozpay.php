<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fengwozpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType        = 'other';

        $data                      = [];
        $data['merchant_no']       = $payConf['business_num'];
        $data['merchant_order_sn'] = $order;
        $data['type']              = $bank;
        $data['total']             = number_format(floatval($amount), 2, '.', '');
        $data['body']              = 'mf';
        $data['url_notify']        = $ServerUrl;
        $data['attach']            = 'mf';
        $data['sign']              = strtoupper(md5("attach={$data['attach']}&body={$data['body']}&merchant_no={$data['merchant_no']}&merchant_order_sn={$data['merchant_order_sn']}&total={$data['total']}&type={$data['type']}&url_notify={$data['url_notify']}{$payConf['md5_private_key']}"));
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data                      = $request->all();
        $arr                       = json_decode($data['data'],true);
        $data['total']             = $arr['total'];
        $data['merchant_order_sn'] = $arr['merchant_order_sn'];
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] != '200') {
            return $data;
        }
        $data['merchant_order_sn'] = $data['data']['merchant_order_sn'];
        $data['order_sn']          = $data['data']['order_sn'];
        $data['qr_code']           = $data['data']['qr_code']; // 生成二维码
        $data['wap_pay_url']       = $data['data']['wap_pay_url']; // H5跳转
        return $data;
    }

}