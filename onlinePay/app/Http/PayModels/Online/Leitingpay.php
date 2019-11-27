<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Leitingpay extends ApiModel
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

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';

        $data['businessId'] = $payConf['business_num'];
        $data['amount'] = number_format(floatval($amount),2, '.', '');
        $data['payType'] = $bank;
        $data['childOrderno'] = $order;
        $data['time'] = date('YmdHis');
        $data['serverUrl'] = $ServerUrl;
        $data['returnUrl'] = $returnUrl;
        $sign = "businessId={$data['businessId']}&payType={$data['payType']}&childOrderno={$data['childOrderno']}&amount={$data['amount']}&time={$data['time']}&{$payConf['md5_private_key']}";
        $data['sign'] = md5($sign);

        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['childOrderno'] = $data['childOrderno'];
        $postData['amount']  = $data['amount'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0') {
            $data['qrCode'] = $data['data']['payUrl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        return $res;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $res = file_get_contents('php://input');
        $data = json_decode($res, true);
        $sign = $data['sign'];
        $str = "childOrderno={$data['childOrderno']}&payAmount={$data['payAmount']}&orderStatus={$data['orderStatus']}&time={$data['time']}&{$payConf['md5_private_key']}";
        $signTrue    = md5($str);

        if (strtoupper($sign) == strtoupper($signTrue) && $data['orderStatus'] == 1) {
            return true;
        }
        return false;
    }


}