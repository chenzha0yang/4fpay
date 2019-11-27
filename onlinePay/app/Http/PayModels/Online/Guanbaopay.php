<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Guanbaopay extends ApiModel
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
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something
        self::$unit    = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$isAPP   = true;

        $data                = [];
        $data['subject']     = $order;
        $data['cpId']        = $payConf['business_num'];
        $data['orderIdCp']   = $order;
        $data['money']       = $amount * 100;
        $data['channel']     = $bank;
        $data['version']     = '1';
        $data['description'] = $order;
        $data['ip']          = self::getIp();
        $data['timestamp']   = time() . mt_rand(100, 999);
        $data['command']     = 'applyqr';
        $data['notifyUrl']   = $ServerUrl;
        $data['returnurl']   = $returnUrl;
        $signStr             = self::getSignStr($data, true, true);
        $data['sign']        = strtoupper(self::getMd5Sign($signStr . '&', $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $arr          = $request->all();
        $arr['money'] = $arr['money'] / 100;
        return $arr;
    }

    /***
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $signStr  = 'money=' . $data['money'] . '&orderIdCp=' . $data['orderIdCp'] .
            '&version=' . $data['version'] . '&' . $payConf['md5_private_key'];
        $signTrue = strtoupper(md5($signStr));
        if ($data['sign'] == $signTrue && $data['status'] == 0) {
            return true;
        } else {
            return false;
        }
    }

}
