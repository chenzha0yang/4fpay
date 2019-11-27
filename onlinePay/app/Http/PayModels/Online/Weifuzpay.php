<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Weifuzpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址


        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$unit    = 2;
        self::$method  = 'header';
        self::$isAPP   = true;

        $tokenData               = $data = $post = [];
        $tokenData['merchantNo'] = $payConf['business_num']; //商户号
        $tokenData['key']        = $payConf['md5_private_key']; //密钥
        $tokenData['nonce']      = rand();
        $tokenData['timestamp']  = date("YmdHis");
        $tokenData['sign']       = strtoupper(md5("merchantNo={$tokenData['merchantNo']}&nonce={$tokenData['nonce']}&timestamp={$tokenData['timestamp']}&key={$payConf['md5_private_key']}"));
        $tokenData['token']      = '';
        $tokUrl                  = 'http://api.jinxiangweb.cn:8888/api/v1/getAccessToken/merchant';
        $header                  = array('Content-Type: application/json; charset=utf-8');
        $restoken                = self::httpGet($tokUrl, json_encode($tokenData), $header);
        $result                  = json_decode($restoken, true);
        if ($result['success'] == true) {
            //请求支付接口
            $post['accessToken'] = $result['value']['accessToken']; //token
            $data['outTradeNo']  = $order;
            $data['money']       = (string)($amount * 100);
            $data['type']        = 'T1';
            $data['body']        = 'wf';
            $data['detail']      = 'wf';
            $data['notifyUrl']   = $ServerUrl;
            $data['productId']   = $order;
            $data['successUrl']  = $returnUrl;
            $post['param']       = $data;
        } else {
            dd($restoken); //获取token失败
        }

        $res['data'] =  json_encode($post);
        $res['httpHeaders'] = [
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        ];
        $res['outTradeNo']  = $order;
        $res['money']       = $data['money'];
        unset($reqData);
        return $res;
    }


    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['success']) {
            $result['qrcode'] = $result['value'];
        }
        return $result;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr          = $request->getContent();
        $res          = json_decode($arr, true);
        $res['money'] = $res['money'] / 100;
        return $res;
    }


    public static function httpGet($url, $data = null, $header = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_URL, $url);
        $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 3);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

}