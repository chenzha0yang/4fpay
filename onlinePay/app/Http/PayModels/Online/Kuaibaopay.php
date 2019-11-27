<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kuaibaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$method = 'get';
        self::$unit = 2;
        if (1 == $payConf['is_app']) {  //H5
            self::$isAPP = true;
        }else{
            self::$reqType = 'curl';
            self::$resType = 'other';
            self::$payWay  = $payConf['pay_way'];
        }
        //TODO: do something
        $data = [
            'mchId'      => $payConf['business_num'],   //商户ID
            'pay_type'   => $bank,                      //支付方式
            'amount'     => $amount*100,                //金额
            'time'       => time(),
            'tradeNo'    => $order,                     //订单号
            'return_url' => $returnUrl,                 //同步通知地址
            'notify_url' => $ServerUrl,                 //异步通知地址
            'client_ip'  => '127.0.0.1'
        ];

        $data['sign'] = md5($data['tradeNo'].'|'.$data['amount'].'|'.$data['pay_type'].'|'.$data['time'].'|'.$data['mchId'].'|'.md5($payConf['md5_private_key'])); //MD5签名

        unset($reqData);
        return $data;
    }

    /**
     * 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $result = json_decode($response,true);
        if(true == $result['ok'] && true == $result['net']){
            $data['url'] = $result['data']['url'];
            $data['img'] = $result['data']['img'];
        }else{
            $data['code'] = $result['code'];
            $data['msg']  = $result['msg'];
        }
        return $data;
    }

    /**
     * 回调特殊处理
     * @param $model
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model,$data,$payConf){
        $signStr = $data['tradeNo'].'|'.$data['status'].'|'.$data['orderNo'].'|'.$data['amount'].'|'.$data['mchId'].'|'.$data['pay_type'].'|'.$data['time'].'|'.md5($payConf['md5_private_key']);
        $sign    = md5($signStr);
        if($sign == $data['sign'] && '1' == $data['status']){
            return true;
        }else{
            return false;
        }
    }
}