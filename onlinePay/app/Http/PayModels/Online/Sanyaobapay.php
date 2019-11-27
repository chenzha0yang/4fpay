<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\Curl;

class Sanyaobapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0;  //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /*    */
    public static $reqData = [];

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

        //TODO: do something
        self::$isAPP    = true;
        self::$payWay   = $payConf['pay_way'];
        self::$unit     = 2;
        self::$method   = 'HEADER';
        self::$resType  = 'other';
        self::$postType = true;

        $data                    = [];
        $data['requestType']     = '1';
        $data['merchantCode']    = $payConf['business_num'];
        $data['merchantOrderNo'] = $order;
        $data['payType']         = $bank;
        $data['payPrice']        = $amount * 100;
        $data['merchantName']    = 'apple';
        $data['callbackUrl']     = $ServerUrl;
        $data['merchantRemark']  = $returnUrl;
        $data['sign']            = md5("merchantName={$data['merchantName']}&merchantCode={$data['merchantCode']}&merchantOrderNo={$data['merchantOrderNo']}&payPrice={$data['payPrice']}&payType={$data['payType']}&{$payConf['md5_private_key']}");
        $headers                 = ['Content-Type: application/json; charset=utf-8'];
        Curl::$header            = $headers;
        Curl::$request           = json_encode($data);//提交数据
        Curl::$url               = $reqData['formUrl'] . '/server/create/pay/order';//支付网关
        Curl::$method            = 'header';//提交方式
        $post                    = [];
        $post['res']             = Curl::Request();
        $post['merchantOrderNo'] = $data['merchantOrderNo'];
        $post['payPrice']        = $data['payPrice'];
        unset($reqData);
        return $post;
    }

    /**
     * @param $post
     * @return mixed
     */
    public static function getRequestByType($post)
    {
        return $post['res'];
    }


    /**
     * @param $resp
     * @return mixed
     */
    public static function getQrCode($resp)
    {
        $result = json_decode($resp['data']['res'], true);
        if ($result['result'] == '0') {
            $result['payPic'] = $result['datas']['payPic'];
        }
        return $result;
    }


    /**
     * @param $request
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $res             = $request->all();
        $res['payPrice'] = $res['payPrice'] / 100;
        return $res;
    }


    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $signStr  = "merchantName={$data['merchantName']}&merchantOrderNo={$data['merchantOrderNo']}&noticeType={$data['noticeType']}&payPrice={$data['payPrice']}&payType={$data['payType']}&{$payConf['md5_private_key']}";
        $signTrue = md5($signStr);
        if ($data['sign'] == $signTrue && $data['noticeType'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}