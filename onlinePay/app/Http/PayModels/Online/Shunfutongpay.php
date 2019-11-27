<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Shunfutongpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method = 'header';

        //TODO: do something
        $data = [
            'merchantNo'  => $payConf['business_num'],   //商户号
            'nonceStr'    => self::randStr(20),   //随机字符串
            'paymentType' => $bank,                      //支付方式
            'mchOrderNo'  => $order,                     //订单号
            'orderTime'   => date('YmdHis'),     //提交时间
            'goodsName'   => 'VIP',
            'amount'      => $amount*100,                    //金额
            'clientIp'    => '127.0.0.1',
            'notifyUrl'   => $ServerUrl,                 //异步通知地址
            'buyerId'     => $payConf['message1'],
            'buyerName'   => $payConf['message2'],

        ];

        $signStr = self::getSignStr($data,true,true);
        $data['sign'] = strtoupper(self::getMd5Sign("{$signStr}&appkey=", $payConf['md5_private_key']));

        $result['data'] = json_encode($data);
        $result['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
        );
        $result['amount'] = $data['amount'];
        $result['mchOrderNo'] = $data['mchOrderNo'];

        unset($reqData);
        return $result;
    }

    /**
     * 二维码及语言包处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res){
        $responseData = json_decode($res,true);
        if ($responseData["returnCode"] =='SUCCESS' && $responseData["resultCode"] == "CREATE_SUCCESS"){
            $data['payUrl'] = $responseData['payUrl'];
        }else{
            $data['returnCode'] = $responseData['returnCode'];
            $data['returnMsg']  = $responseData['returnMsg'];
        }

        return $data;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $data = $request->all();
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $signInfo = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data,true,true);
        $sign = strtoupper(self::getMd5Sign("{$signStr}&appkey=", $payConf['md5_private_key']));
        if ($sign == $signInfo && $data['orderStatusCode'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }

}