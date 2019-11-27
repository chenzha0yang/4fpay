<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Meimeipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array =[];

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

        self::$reqType = 'curl';
        self::$method = 'header';
        self::$payWay = $payConf['pay_way'];
        self::$isAPP = true;

        $data['version'] = '1.0.0';
        $data['merId'] = $payConf['business_num'];
        $data['orderNo'] = $order;
        $data['orderAmt'] = $amount;
        $data['thirdChannel'] = 'alibaba';
        $data['remark1'] = 'clear1';
        $data['remark2'] = 'clear2';
        $data['notifyUrl'] = $ServerUrl;
        $data['callbackUrl'] = $returnUrl;
        $data['payprod'] = $bank;

        $md5str         = self::getSignStr($data, true, true);
        $data['sign']   = md5($md5str.'&key='. $payConf['md5_private_key']);
        $post['data'] = json_encode($data);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
        );
        $post['orderAmt'] = $data['orderAmt'];
        $post['orderNo'] = $data['orderNo'];
        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data =  json_decode($arr,  true);
        self::$array = $data;
        return $data;
    }


    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];
        unset($data['sign']);
        $md5str         = self::getSignStr($data, true, true);
        $isSign   =md5($md5str.'&key='. $payConf['md5_private_key']);
        if ( strtoupper($sign) == strtoupper($isSign) && $data['payStatus'] == 'S' ) {
            return true;
        }
        return false;
    }
}