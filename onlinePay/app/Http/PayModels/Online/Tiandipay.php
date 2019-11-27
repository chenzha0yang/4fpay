<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tiandipay extends ApiModel
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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址


        self::$reqType = 'curl';
        self::$unit = 2;
        self::$postType = true;
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        $data['merId'] = $payConf['business_num'];//商户号
        $data['prdOrNo'] = $order;//订单号
        $data['orderTime'] = date("Y-m-d H:i:s"); //订单时间

        if((int)$payConf['pay_way'] === 1){
            $data['bank_code'] = $bank; //支付编码
            $data['payCode'] = '';
        } else {
            $data['payCode'] = $bank;//支付编码
        }
        $data['orderAmount'] = $amount * 100;//订单金额-以分为单位，
        $data['userOrderIP'] = self::getIp();//商户订单ip
        $data['asyncNotifyUrl'] = $ServerUrl;// 异步回调
        $data['syncNotifyUrl'] = $returnUrl;// 同步返回
        $signStr = self::getSignStr($data, true, true);
        $data['signStr'] = strtoupper(md5($signStr . "&key=" . $payConf['md5_private_key']));//MD5签名 全大写

        unset($reqData);
        return $data;
    }


    public static function getRequestByType($data)
    {
        $o="";
        foreach ($data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";
        }
        return substr($o,0,-1);
    }

    public static function getQrCode($res)
    {
        $result = json_decode($res, true);
        if($result['code'] == 1){
            $result['code_url'] = $result['msg'];
        }
        return $result;
    }

    public static function getVerifyResult($request, $mod)
    {
        $request['orderAmount'] = $request['orderAmount']/100;
        return $request;
    }
}