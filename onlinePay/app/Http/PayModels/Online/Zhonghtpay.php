<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zhonghtpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址

        $data = [];
        $data['merchantId'] = $payConf['business_num'];//商户号
        $data['totalAmount'] = number_format($amount,2,'.','');
        $data['desc'] = 'vivo';//描述
        $data['corp_flow_no'] = $order;//订单
        $data['model'] = $bank;//支付类型
        $data['notify_url'] = $ServerUrl;
        $data['client_ip'] = '127.0.0.1';
        $signStr = $data['merchantId']."pay".$data['totalAmount'].$data['corp_flow_no'];
        $data['sign'] = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        if($payConf['is_app'] == '1'){
            self::$isAPP = true;
        }
        unset($reqData);
        return $data;
    }

    public static function getQrCode($res){
        $result = json_decode($res,true);
        if($result['Code'] == '00'){
           $result['qrcode'] = $result['Msg'];
        }
        return $result;
    }

}