<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Dadazfpay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method = 'header';
        self::$resType = 'other';

        $data['mchId']     = $payConf['business_num'];
        $data['tradeType'] = $bank;
        if ($payConf['pay_way'] == '0') {
            $data['tradeType'] = '02';
            $data['bankId']    = $bank;
        }
        $data['orderName']   = $order;
        $data['orderMemo']   = $order;
        $data['tradeNo']     = $order;
        $data['totalFee']    = $amount * 100;
        $data['notifyUrl']   = $ServerUrl;
        $data['returnUrl']   = $returnUrl;
        $data['signType']    = '0';
        $signStr             = self::getSignStr($data,true,true);
        $data['sign']        = md5($signStr . '&key='.$payConf['md5_private_key']);
        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];

        $post['data']        = $jsonData;
        $post['httpHeaders'] = $header;
        $post['tradeNo'] = $order;
        $post['totalFee']     = $data['totalFee'];
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
        if ($result['code'] == '0') {
            $result['qrcode'] = $result['body']['jumpUrl'];
        }
        return $result;
    }

    public static function getVerifyResult($request)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if($data){
            $data['orderNo'] = $data['orderNo'];
            $data['totalFree'] = $data['totalFree']/100;
        }else{
            $data = array(
                'orderNo'=>'',
                'totalFree' => ''
            );
        }
        return $data;
    }

    public static function signOther($model, $datas, $payConf)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data,true,true);
        $signTrue = md5($signStr . '&key=' . $payConf['md5_private_key']);
        if (strtoupper($signTrue) == strtoupper($sign)) {
            return true;
        }
        return false;
    }

}