<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fulezfvpay extends ApiModel
{
    public static $method = 'get'; //提交方式 必加属性 post or get

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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        //TODO: do something

        $data['uid']        = $payConf['business_num'];
        $data['price']      = $amount;
        $data['order_id']   = $order;
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $data['sign']       = md5($data['uid'] . $data['price'] . $data['order_id'] . $data['notify_url'] . $data['return_url'] . $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = md5($data['order_id'] . $data['price'] . $data['txnTime'] .$payConf['md5_private_key']);
        if ($data['sign'] == $sign) {
            return true;
        } else {
            return false;
        }
    }

}