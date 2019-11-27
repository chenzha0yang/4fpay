<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huifengpay extends ApiModel
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

        $data['merchant'] = $payConf['business_num'];
        $data['qrtype']   = $bank;
        if ((int)$payConf['pay_way'] === 1) {
            $data['qrtype'] = 'wy';
        }
        $data['customno']    = $order;
        $data['money']       = $amount;
        $data['sendtime']    = time();
        $data['notifyurl']   = $ServerUrl;
        $data['backurl']     = $returnUrl;
        $data['risklevel']   = '';
        $signStr             = "merchant={$data['merchant']}&qrtype={$data['qrtype']}&customno={$data['customno']}&money={$data['money']}&sendtime={$data['sendtime']}&notifyurl={$data['notifyurl']}&backurl={$data['backurl']}&risklevel={$data['risklevel']}{$payConf['md5_private_key']}";
        $data['sign']        = md5($signStr);

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign    = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, false, false);
        $signTrue  = md5($signStr.$payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($signTrue)  && $data['state'] == 1) {
            return true;
        }
        return false;
    }


}