<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Sanshipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        //TODO: do something
        self::$method  = 'header';
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$isAPP   = true;
        self::$resType = 'other';

        $data                    = [];
        $data['merchantNo']      = $payConf['business_num'];
        $data['merchantOrderNo'] = $order;
        $data['orderAmount']     = $amount;
        $data['payNotifyUrl']    = $ServerUrl;
        $data['payType']         = $bank;
        $data['subjectName']     = 'ss';
        $data['extraParam']      = 'ss';
        $signStr                 = "merchantNo={$data['merchantNo']}&merchantOrderNo={$data['merchantOrderNo']}&orderAmount={$data['orderAmount']}&payNotifyUrl={$data['payNotifyUrl']}&payType={$data['payType']}&subjectName={$data['subjectName']}&";
        $data['sign']            = strtoupper(self::getMd5Sign($signStr, $payConf['md5_private_key']));
        $post                    = [];
        $post['data']            = json_encode($data);
        $post['httpHeaders']     = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post['data'])];
        $post['merchantOrderNo'] = $data['merchantOrderNo'];
        $post['orderAmount']     = $data['orderAmount'];
        unset($reqData);
        return $post;
    }

    /***
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {
        $arr           = json_decode($res, true);
        $arr['payUrl'] = $arr['data']['payUrl'];
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
        $sign     = $data['sign'];
        $signStr  = "extraParam={$data['extraParam']}&merchantNo={$data['merchantNo']}&merchantOrderNo={$data['merchantOrderNo']}&orderAmount={$data['orderAmount']}&orderRealAmount={$data['orderRealAmount']}&payNotifyUrl={$data['payNotifyUrl']}&payType={$data['payType']}&subjectName={$data['subjectName']}&{$payConf['md5_private_key']}";
        $signTrue = md5($signStr);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }
}