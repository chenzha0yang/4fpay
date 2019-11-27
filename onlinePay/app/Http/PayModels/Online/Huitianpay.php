<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huitianpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        self::$reqType = 'get';

        $data                = [];
        $data['P_UserId']    = $payConf['business_num'];
        $data['P_OrderId']   = $order;
        $data['P_CardId']    = 'dsn';
        $data['P_CardPass']  = 'kami';
        $data['P_FaceValue'] = sprintf("%.2f", $amount);
        $data['P_ChannelId'] = $bank;
        if ($payConf['pay_way'] == '1') {
            $data['P_ChannelId']   = '1';
            $data['P_Description'] = $bank;
        }
        $data['P_Subject']    = 'honor';
        $data['P_Price']      = sprintf("%.2f", $amount);
        $data['P_Quantity']   = '1';
        $data['P_Result_URL'] = $ServerUrl;
        $data['P_Notify_URL'] = $returnUrl;
        $P_PostKey            = $data['P_UserId'] . '|' . $data['P_OrderId'] . '|' . $data['P_CardId'] . '|' . $data['P_CardPass'] . '|' . $data['P_FaceValue'] . '|' . $data['P_ChannelId'] . '|' . $payConf['md5_private_key'];
        $data['P_PostKey']    = MD5($P_PostKey);
        unset($reqData);
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $OrderId   = $data["P_OrderId"];
        $CardId    = $data["P_CardId"];
        $CardPass  = $data["P_CardPass"];
        $FaceValue = $data["P_FaceValue"];
        $ChannelId = $data["P_ChannelId"];
        $ErrCode   = $data["P_ErrCode"];
        $payMoney  = $data["P_PayMoney"];
        $UserId    = $data["P_UserId"];
        $PostKey   = $data["P_PostKey"];
        $money     = sprintf("%.2f", $FaceValue);
        echo "ErrCode=0";
        $P_PostKey = $UserId . "|" . $OrderId . "|" . $CardId . "|" . $CardPass . "|" . $FaceValue . "|" . $ChannelId . "|" . $payMoney . "|" . $ErrCode . "|" . $payConf['md5_private_key'];
        $sign      = md5($P_PostKey);
        if ($sign == $PostKey && $ErrCode == '0') {
            return true;
        } else {
            return false;
        }
    }
}
