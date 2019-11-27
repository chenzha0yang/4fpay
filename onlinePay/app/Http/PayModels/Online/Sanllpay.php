<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Sanllpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = [];

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
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$postType  = true;


        $data['version']      = '1.0.0';
        $data['merId']        = $payConf['business_num'];
        $data['orderNo']      = $order;
        $data['orderAmt']     = $amount;
        $data['thirdChannel'] = $bank;
        $data['remark1']      = 'remark';
        $data['remark2']      = 'remark';
        $data['notifyUrl']    = $ServerUrl;
        $data['callbackUrl']  = $returnUrl;
        $data['payprod']      = '11';
        if (self::$isAPP) {
            $data['payprod'] = '10';
        }
        $buff                = self::getSignStr($data, true, true);
        $string              = $buff . "&key=" . $payConf['md5_private_key'];
        $data['sign']        = md5($string);

        unset($reqData);
        return $data;
    }


    public static function getRequestByType ($data) {
        return json_encode($data);
    }



    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $json, $payConf)
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        ksort($data);
        $buff = '';
        foreach ($data as $key => $val) {
            if ($key != "sign" && $val != "") {
                $buff .= $key . "=" . $val . "&";
            }
        }
        $string = $buff . "key=" . $payConf['md5_private_key'];
        $sign   = md5($string);
        if (strtolower($sign) == strtolower($data['sign']) && $data['payStatus'] == 'S') {
            return true;
        } else {
            return false;
        }
    }
}