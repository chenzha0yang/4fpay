<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Shunfzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

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
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';
        self::$resType = 'other';
        self::$unit    = 2;


        $data['version'] = 'V2.0.0';
        $data['merchantNumber'] = $payConf['business_num'];//商户号
        $data['payType'] = $bank;//银行编码
        $data['orderNumber'] = $order;//订单号
        $data['amount'] = $amount*100;//订单金额
        $data['currency'] = 'CNY';
        $data['commodityName'] = 'test';
        $data['commodityDesc'] = 'test';
        $data['notifyUrl'] = $ServerUrl;
        $data['orderCreateIp'] = self::getIp();
        $data['key'] = $payConf['md5_private_key'];
        $signStr =  self::getSignStr($data, true, true);
        $data['sign'] = md5($signStr);
        unset($data['key']);

        $post['order']       = $order;
        $post['amount']      = $data['amount'];
        $post['data']        = json_encode($data);
        $post['httpHeaders'] = [
            "Content-Type: application/json; charset=utf-8"
        ];
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
        if ($result['success']) {
            $result['payUrl'] = $result['context']['payurl'];
        }else{
            $result['msg'] = $result['message']['content'];
            $result['code'] = $result['message']['code'];
        }
        return $result;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        if(isset($res['money'])){
            $res['money'] = $res['money'] / 100;
        }else{
            $res['money'] = '';
        }

        if(isset($res['merchantOrderNumber'])){
            $res['merchantOrderNumber'] = $res['merchantOrderNumber'];
        }else{
            $res['merchantOrderNumber'] = '';
        }
        return $res;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $json, $payConf)
    {
        $post = file_get_contents('php://input');
        $resData = json_decode($post,true);
        $sign = $resData['sign'];
        unset($resData['sign']);
        $resData['key'] = $payConf['md5_private_key'];
        $signStr =  self::getSignStr($resData, true, true);
        $signTrue = md5($signStr);
        if (strtolower($sign) == strtolower($signTrue) && $resData['orderStatus'] == 'SUC') {
            return true;
        } else {
            return false;
        }
    }
}