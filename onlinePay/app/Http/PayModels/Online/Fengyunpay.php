<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fengyunpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        self::$isAPP          = true;
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$unit           = 2;
        $aa                   = explode('@', $payConf['business_num']);
        $data['merId']        = $aa[0];//商户号
        $data['terId']        = $aa[1];//终端号
        if (!isset($aa[1])) {
            echo '绑定格式错误!请参考:商户号@终端号';
            exit();
        }
        $data['businessOrdid'] = $order;
        $data['orderName']     = 'goods';
        $data['payType']       = $bank;
        $data['asynURL']       = $ServerUrl;
        $data['syncURL']       = $returnUrl;
        $data['appSence']      = '1001'; //pc
        if ($payConf['is_app'] == 1) {
            $data['appSence'] = '1002'; //h5
        }
        $data['tradeMoney'] = $amount * 100;
        $signStr            = self::getSignStr($data, true, true);
        $data['sign']       = self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request)
    {
        $data          = $request->all();
        $data['money'] = $data['money'] / 100;
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['signType']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = self::getMd5Sign($signStr . "&key=", $payConf['md5_private_key']);
        if ($mySign == $sign && $data['order_state'] == '1003') {
            return true;
        } else {
            return false;
        }
    }
}