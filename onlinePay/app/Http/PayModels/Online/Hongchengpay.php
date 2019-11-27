<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hongchengpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $UserName = '';

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        self::$reqType = 'curl'; // 有即时返回json时，用curl请求
        self::$resType = 'other'; //请求支付时，返回来的即时json数据，做特殊处理
        self::$payWay = $payConf['pay_way']; //生成二维码

        self::$isAPP = true;
        self::$httpBuildQuery = true;
        $data['channel_id'] = $payConf['business_num'];//商户编号
        $data['pay_trench'] = $bank;//支付类型
        $data['timestamp'] = date('YmdHis',time());
        $data['out_bill_num'] = $order; //订单号
        $data['amount'] = sprintf('%.2f',$amount);//订单金额,保留2位小数
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    /**
     * 二维码、链接处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {
        $result = json_decode($res, true);

            if ( $result['response_code']== '1001' ) {
                $data['payUrl'] = $result['response_data']['pay_url'];
            }else{
                $data['response_code'] = $result['response_code'];
                $data['response_msg'] = $result['response_msg'];
            }
            return $data;

    }



    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);

        $mySign = strtoupper(self::getMd5Sign("{$signStr}", $payConf['md5_private_key']));
        if ( strtoupper($sign) == $mySign && $data['pay_status'] == 1) {
            return true;
        } else {
            return false;
        }
    }



}