<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shoufujinliupay extends ApiModel
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

        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        
        $data['MerTradeID']     = $order;//订单号
        $data['MerProductID']      = 'test';
        $data['MerUserID'] = self::$UserName;
        $data['Amount']       = $amount;//订单金额
        $data['ValidateKey'] = $payConf['public_key'];

        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $key .'='.$value.'&';
        }

        $signStr = substr($signStr,0,strlen($signStr)-1); 

        $data['HashIV']     = $payConf['business_num'];//商户号
        $data['HashKey']     = $payConf['md5_private_key'];//商户号
        $data['VerifyCode'] = md5($signStr);

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['Validate'];
        unset($data['Validate']);
        $signStr  = 'ValidateKey='.$payConf['public_key'].'&HashKey='.$payConf['md5_private_key'].'&RtnCode='.$data['RtnCode'].'&TradeID='.$data['MerTradeID'].'&UserID='.$data['MerUserID'].'&Money='.$data['Amount'];
        $signTrue = md5($signStr);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['RtnCode'] == 1) {
            return true;
        }
        return false;
    }


}