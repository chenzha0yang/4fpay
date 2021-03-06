<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bailongmapay extends ApiModel
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



        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['UID'] = strtolower($payConf['business_num']) ;
        $data['type'] = $bank;
        $data['money'] = $amount;
        $data['external'] = $order;
        $data['notifyUrl'] = $ServerUrl;

        // 1. 对加密数组进行字典排序
        foreach ($data as $key=>$value){
            $arr[$key] = $key;
        }
        sort($arr); //字典排序的作用就是防止因为参数顺序不一致而导致下面拼接加密不同

        $str = "";
        foreach ($arr as $k => $v) {
            $str = $str.strtolower($arr[$k]).'='.urlencode($data[$v]).'&';
        }
        $data['sign']=md5($payConf['md5_private_key'].substr($str,0,-1));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);

        if ($data['code'] == '200') {
            $data['img'] = $data['data']['img'];
            $data['img'] = $data['data']['img'];
        }
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        // 1. 对加密数组进行字典排序
        foreach ($data as $key=>$value){
            $arr[$key] = $key;
        }
        sort($arr);  //字典排序的作用就是防止因为参数顺序不一致而导致下面拼接加密不同

        $str = "";
        foreach ($arr as $k => $v) {
            $str = $str.strtolower($arr[$k]).'='.urlencode($data[$v]).'&';
        }
        $mySign=md5($payConf['md5_private_key'].substr($str,0,-1));

        if ($sign == $mySign && $data['status'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }

    }

}