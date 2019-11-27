<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $res = explode('@', $payConf['business_num']);
        $data['identification'] = $res[1];
        $data['type'] = $bank;
        $data['price'] = $amount * 100;
        $data['orderuid'] = $order;
        $data['goodsname'] = 'goods';
        $data['orderid'] = $order;
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $data['key'] = md5($data['goodsname'] . $data['identification'] . $data['notify_url'] . $data['orderid'] . $data['orderuid'] . $data['price'] . $data['return_url'] . $payConf['md5_private_key'] . $data['type']);
        unset($reqData);
        return $data;
    }

    //回调金额化分为元 
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if(isset($arr['price'])){
            $data['price'] = $arr['price'] / 100;
        }else{
            $data['price'] = '';
        }
        if(isset($arr['orderid'])){
            $data['orderid'] = $arr['orderid'];
        }else{
            $data['orderid'] = '';
        }
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['key'];
        $signStr = md5($data['actual_price'] . $data['bill_no']. $data['orderid'] . $data['orderuid'] . $data['price']. $payConf['md5_private_key']);
        if (strtolower($sign) == strtolower($signStr)) {
            return true;
        } else {
            return false;
        }
    }

}