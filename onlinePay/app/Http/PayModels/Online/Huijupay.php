<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huijupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];

        $data = [];
        $params["p0_Version"] = '1.0';                          //版本号
        $params["p1_MerchantNo"] = $payConf['business_num'];    //商户号
        $params["p2_OrderNo"] = $order;                         //订单
        $params["p3_Amount"] = $amount;
        $params["p4_Cur"] = '1';                                //默认设置为1
        $params["p5_ProductName"] = 'vivo';
        $params["p6_ProductDesc"] = 'qh2000';
        $params["p7_Mp"] = $order;                              //回传参数
        $params["p8_ReturnUrl"] = '';
        $params["p9_NotifyUrl"] = $ServerUrl;
        $params["q1_FrpCode"] = $bank;
        $params["q2_MerchantBankCode"] = '';
        $params["q3_SubMerchantNo"] = '';
        $params["q4_IsShowPic"] = '';
        $params["q5_OpenId"] = '';
        $params["q6_AuthCode"] = '';
        $params["q7_AppId"] = '';
        $params["q8_TerminalNo"] = '';
        $sign = md5(implode("", $params) . $payConf['md5_private_key']);
        $params['hmac'] = urlencode($sign);                     //签名
        unset($reqData);
        return $params;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $hmac = $data['hmac'];
        unset($data['hmac']);
        $data['ra_PayTime'] = urldecode($data['ra_PayTime']);
        $data['rb_DealTime'] = urldecode($data['rb_DealTime']);
        $sign = md5(implode("", $data) . $payConf['md5_private_key']);
        if ( $sign == $hmac && $data['r6_Status'] == '100' ) {
            return true;
        } else {
            return false;
        }
    }
}