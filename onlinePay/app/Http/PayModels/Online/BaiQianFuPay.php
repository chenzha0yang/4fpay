<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\SignCheck;

class BaiQianFuPay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址
        //TODO: do something
        self::$reqType          = 'curl';
        self::$payWay           = $payConf['pay_way'];

        $data                   = array(
            'X1_Amount'    => sprintf('%.2f', $amount),
            'X2_BillNo'    => $order,
            'X3_MerNo'     => $payConf['business_num'],//商户号
            'X4_ReturnURL' => $ServerUrl,
        );
        $strToSign              = self::getSignStr($data, false, true);
        $data['X5_NotifyURL']   = $returnUrl;
        $data['X7_PaymentType'] = $bank;
        $data['X8_MerRemark']   = 'dulex';
        $data['X9_ClientIp']    = self::getIp();
        $data['X6_MD5info']     = strtoupper(md5($strToSign . '&' . strtoupper(md5($payConf['md5_private_key']))));
        unset($reqData);
        return $data;
    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $data = file_get_contents("php://input");
        $result = json_decode($data,true);
        return $result;
    }


    /**
     * 特殊MD5加密方法重写
     * @param $mod
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $data, $payConf)
    {
        $data = file_get_contents("php://input");
        $result = json_decode($data,true);
        $sign   = $result['MD5info'];
        $dataRe = array(
            "Amount"    => $result['Amount'],
            "BillNo"    => $result['BillNo'],
            "MerNo"     => $result['MerNo'],
            "Succeed"   => $result['Succeed']
        );
        $signStr = self::getSignStr($dataRe, true, true);
        $mySign  = strtoupper(md5($signStr . '&' . strtoupper(md5($payConf['md5_private_key']))));
        if ($mySign == $sign && $result['Succeed'] == '88') {
            SignCheck::$sign['Succeed'] = '88';
            return true;
        }
        SignCheck::$sign['Succeed'] = '';
        return false;
    }
}