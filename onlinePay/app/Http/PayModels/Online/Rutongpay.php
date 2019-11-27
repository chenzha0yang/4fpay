<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Rutongpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = [];

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method  = 'header';
        //TODO: do something

        $data = [];
        $data['merchNo'] = $payConf['business_num'];
        $data['orderNo'] = $order;
        $data['amount'] = $amount;
        $data['currency'] = 'CNY';
        $data['outChannel'] = $bank;
        $data['title'] = 'qaq';
        $data['product'] = 'goodsName';
        $data['memo'] = $amount;
        $data['returnUrl'] = $returnUrl;
        $data['notifyUrl'] = $ServerUrl;
        $data['reqTime'] = date('Ymdhis');
        $data['userId'] = '123456';
        ksort($data);
        $context = json_encode($data);
        $arr['sign'] = md5($context . $payConf['md5_private_key']);
        $arr['context'] = base64_encode($context);
        $post = [];
        $post['data'] = json_encode($arr);
        $post['amount'] = $data['amount'];
        $post['orderNo'] = $data['orderNo'];
        $post['httpHeaders'] = [
            "Content-Type: application/json; charset=utf-8"
        ];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if($result['code'] == 0){
            $res=json_decode($result['context'], true);
            if(isset($res['code_url'])){
                $result['url'] = $res['code_url'];
            }
        }
        return $result;
    }

    //回调金额处理
    public static function getVerifyResult($request)
    {
        $json = $request->getContent();
        $data = json_decode($json, true);
        self::$array=$data;
        $arr = json_decode($data['context'], true);
        $arr['msg'] = $data['msg'];
        $arr['code'] = $data['code'];
        return $arr;
    }

    public static function SignOther($mod, $request, $payConf)
    {
        $data=self::$array;
        $sign = $data['sign'];
        $mySign = md5($data['context']. $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($mySign) && $data['code'] == '0') {
            return true;
        } else {
            return false;
        }
    }
}