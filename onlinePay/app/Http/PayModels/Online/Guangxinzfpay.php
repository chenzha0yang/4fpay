<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Guangxinzfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';
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
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';

        //TODO: do something
        $data['mchNo'] = $payConf['business_num'];
        $data['mchUserNo'] = self::$UserName;
        $data['outTradeNo'] = $order;
        $data['channel'] = $bank;
        $data['amount'] = sprintf('%.2f',$amount);
        $data['body'] = 'test';
        $data['payDate'] = date('YmdHis',time());
        $data['notifyUrl'] = $ServerUrl;
        $data['returnUrl'] = $returnUrl;

        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = md5($signStr . '&signKey=' . $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $data                = $request->all();
        $res['amount']    = $data['amount'] / 100;
        $res['businessnumber'] = $data['businessnumber'];

        return $res;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['remark']);
        $signStr = self::getSignStr($data, true, true);
        $mySign  = md5($signStr . '&signKey=' . $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($mySign) && $data['resultCode'] == '00') {
            return true;
        } else {
            return false;
        }
    }
}