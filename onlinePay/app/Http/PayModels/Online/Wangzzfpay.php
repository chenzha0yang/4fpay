<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wangzzfpay extends ApiModel
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

        self::$isAPP = true;
        self::$method  = 'header';
        self::$reqType = 'curl';
        self::$unit = 2;
        self::$payWay  = $payConf['pay_way'];


        //TODO: do something
        $data['merchno'] = $payConf['business_num'];  //商户号
        $data['traceno'] = $order;
        $data['paytype'] = $bank;
        $data['total_fee'] = $amount * 100;
        $data['notifyurl'] = $ServerUrl;
        $data['returnurl'] = $returnUrl;

        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5(md5($signStr."&key=".$payConf['md5_private_key'])));
        $post['data'] = json_encode($data);
        $post['httpHeaders']   = [
            'Content-Type: application/json'
        ];
        $post['traceno'] = $data['traceno'];
        $post['total_fee'] = $data['total_fee'];
        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data  = $request->all();
        $data['total_fee'] = $data['total_fee'] / 100;
        return $data;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true , true);
        $isSign = strtoupper(md5(md5($signStr."&key=".$payConf['md5_private_key'])));

        if (strtoupper($sign) == $isSign && $data['respCode'] == 'E0000') {
            return true;
        }
        return false;
    }

}