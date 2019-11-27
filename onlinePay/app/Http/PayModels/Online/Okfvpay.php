<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Okfvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $userName = '';
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
        self::$userName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //TODO: do something

        $data['uid'] = $payConf['business_num'];//商户号
        $data['price'] = sprintf('%.2f',$amount);//订单金额
        $data['istype'] = $bank;//银行编码
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $data['orderid'] = $order;//订单号
        $data['orderuid'] = self::$userName;
        $data['goodsname'] = 'test';
        $data['version'] = 2;
        $data['ip'] = self::getIp();
        $data['token'] = $payConf['md5_private_key'];
        $signStr =  self::getSignStr($data,true,true);
        $data['key'] = md5($signStr);

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['key'];
        unset($data['key']);
        $status = $data['status'];
        unset($data['status']);
        $data['token'] = $payConf['md5_private_key'];
        $signStr =  self::getSignStr($data, true, true);
        $signTrue = md5($signStr);
        if (strtoupper($sign) == strtoupper($signTrue) && $status == '3') {
            return true;
        }
        return false;
    }

}