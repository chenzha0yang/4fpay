<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jufbaofupay extends ApiModel
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
    public static function getAllInfo ($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something
        $data = array();
        $data['customerno'] = $payConf['business_num'];
        $data['devicetype'] = 'apple';
        $data['customerbillno'] = $order;
        $data['customeruser'] = $payConf['business_num'];
        $data['customerbilltime'] = date('Y-m-d H:i:s');
        $data['ip'] = self ::getIp();
        $data['orderamount'] = sprintf('%.2f', $amount);
        $data['channeltype'] = $bank;
        if ( $payConf['pay_way'] == 1 ) {
            $data['channeltype'] = 'onlinebank';
            $data['bankcode'] = $bank;
        }
        $data['notifyurl'] = $ServerUrl;
        $data['returnurl'] = $returnUrl;

        $signStr = self ::getSignStr($data, true, true);
        $data['sign'] = strtolower(md5($signStr . '&key=' . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /***
     * @param $mod
     * @param $data  回调信息
     * @param $payConf 配置信息
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = strtolower(md5($signStr . '&key=' . $payConf['md5_private_key']));
        if ($sign == $signTrue && $data['paystatus'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}