<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Baolongpay extends ApiModel
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

        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $data['mert_no']      = $payConf['business_num'];
        $data['out_trade_no'] = $order;
        $data['pay_type']    = $bank;
        $data['amount']      = $amount * 100;
        $data['order_ip']    = self::getIp();
        $data['notify_url']     = $ServerUrl;
        $data['order_time']    = time();
        $signStr           = self::getSignStr($data, true, true);
        $data['sign']      = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));

        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['out_trade_no'] = $data['out_trade_no'];
        $postData['amount']  = $data['amount'];

        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['state'] == 'ok') {
            if ($data['res_type'] == "qr") {
                $data['qrCode'] = $data['res_data'];
            }
            if ($data['res_type'] == "url") {
                self::$isAPP = true;
                $data['payUrl'] = $data['res_data'];
            }
        }

        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data['out_trade_no'] = $res['out_trade_no'];
        $data['amount'] = $res['amount']/100;
        return $data;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue)  && $data['state'] == "ok") {
            return true;
        }
        return false;
    }


}