<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinhefupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $data = [];

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
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$resType ='other';
        self::$payWay = $payConf['pay_way'];
        //TODO: do something
        //分割出商户Id和应用Id
        $array=explode('@', $payConf['business_num']);
        $data = array(
            'subject'  => 'goodsname',
            'body'  => 'goodnice',
            'productId'     => $bank,
            'notifyUrl' => $ServerUrl,
            'mchOrderNo'    => $order,
            'appId'   => $array[1],
            'amount'      => $amount,
            'mchId'       => $array[0],
            'currency'    => 'cny',
            'clientIp'    => self ::getIp(),
            'device'      => 'WEB',
        );
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function getQrCode($request)
    {
        $res =  json_decode($request,true);
        if(isset($res['payParams']['payUrl'])){
            $res['payUrl'] =$res['payParams']['payUrl'];
        }
        return $res;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        self::$data=$res;
        return $res;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $arr=self::$data;
        $sign = $arr['sign'];
        unset($arr['sign']);
        $signStr =  self::getSignStr($arr, true, true);
        $signTrue = md5($signStr . "&key=" . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $arr['code'] == '6003') {
            return true;
        }
        return false;
    }



}