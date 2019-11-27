<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Kuangbaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signData = [];

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
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';

        //TODO: do something
        $data = [
            'merchant_order_uid'          => $payConf['business_num'],   //商户号
            'merchant_order_sn'         => $order,                     //订单号
            'merchant_order_date'        => '2019-08-09 18:18:18',
            'merchant_order_money'         => $amount,                    //金额
            'merchant_order_callbak_confirm_duein'   => $ServerUrl,                 //异步通知地址
            'merchant_order_channel'         => $bank,                      //支付方式
        ];
        $signStr = self::getSignStr($data, true, true);
        $data['merchant_order_sign'] = md5(strtoupper($signStr .'&apikey='. $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * 二维码及语言包处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $data = json_decode($response,true);
        if ($data["code"] == 200){
            $data['url'] = $data['data']['url'];
        }
        return $data;
    }

    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        self::$signData = $res;
        $res['order_money']=$res['data']['order_money'];
        $res['merchant_order_sn']=$res['data']['merchant_order_sn'];
        return $res;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $data = self::$signData;
        $arr    = json_decode($data,true);
        $sign = $arr['data']['sign'];
        $signStr = "code=".$arr['code']."&merchant_order_sn=".$arr['data']['merchant_order_sn']."&msg=".$arr['msg']."&order_id=".$arr['data']['order_id']."&order_money=".$arr['data']['order_money']."&apikey=".$payConf['md5_private_key'];
        $signTrue = md5(strtoupper($signStr));
        if (strtolower($sign) == $signTrue && $arr['code'] == '200') {
            return true;
        } else {
            return false;
        }
    }

}