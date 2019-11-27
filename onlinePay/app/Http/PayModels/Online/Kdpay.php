<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kdpay extends ApiModel
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
        self::$method = 'get';
        self::$isAPP = true;
        $data = [];

        $UserId  = $payConf['business_num']; //平台商户ID，需要更换成自己的商户ID
        $SalfStr = $payConf['md5_private_key']; //商户密钥
        //充值结果后台通知地址
        $result_url = $ServerUrl;
        //充值结果用户在网站上的转向地址
        $notify_url = $returnUrl;
        $P_CardId   = '';
        $P_CardPass = '';

        $P_FaceValue = $amount; //金额

        $P_ChannelId = $bank; //支付类型
        $P_Bankid = '';
        if ($payConf['pay_way'] == '0') {
            $P_Bankid    = $bank; //银行编码
            $P_ChannelId = '1';
        }
        $P_Subject    = 'chongzhi';
        $P_Price      = $amount;
        $P_Quantity   = 1;
        $P_Result_url = $returnUrl;
        $P_Notify_url = $ServerUrl;
        $P_OrderId    = $order;
        $preEncodeStr = $UserId . "|" . $P_OrderId . "|" . $P_CardId . "|" . $P_CardPass . "|" . $P_FaceValue . "|" . $P_ChannelId . "|" . $SalfStr;
        $P_PostKey    = md5($preEncodeStr);

        $data         = array(
            "P_UserId"      => $UserId,
            "P_OrderId"     => $P_OrderId,
            "P_CardId"      => $P_CardId,
            "P_CardPass"    => $P_CardPass,
            "P_FaceValue"   => $P_FaceValue,
            "P_ChannelId"   => $P_ChannelId,
            "P_Subject"     => $P_Subject,
            "P_Price"       => $P_Price,
            "P_Quantity"    => $P_Quantity,
            "P_Description" => $P_Bankid,
            "P_Notic"       => '',
            "P_Result_url"  => $P_Result_url,
            "P_Notify_url"  => $P_Notify_url,
            "P_PostKey"     => $P_PostKey,
        );

        unset($reqData);
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $order_num   = $data["P_OrderId"];
        $OrderId     = $data["P_OrderId"];
        $CardId      = $data["P_CardId"];
        $CardPass    = $data["P_CardPass"];
        $FaceValue   = $data["P_FaceValue"];
        $ChannelId   = $data["P_ChannelId"];
        $subject     = $data["P_Subject"];
        $description = $data["P_Description"];
        $price       = $data["P_Price"];
        $quantity    = $data["P_Quantity"];
        $notic       = $data["P_Notic"];
        $ErrCode     = $data["P_ErrCode"];
        $PostKey     = $data["P_PostKey"];
        $payMoney    = $data["P_PayMoney"];
        $ErrMsg      = $data["P_ErrMsg"]; //错误描述
        $order_num   = $data["P_OrderId"]; //订单号
        $total_fee   = $data["P_FaceValue"]; //订单金额

        $UserId  = $payConf['business_num']; //平台商户ID，需要更换成自己的商户ID
        $SalfStr = $payConf['md5_private_key']; //商户密钥

        $preEncodeStr = $UserId . "|" . $OrderId . "|" . $CardId . "|" . $CardPass . "|" . $FaceValue . "|" . $ChannelId . "|" . $payMoney . "|" . $ErrCode . "|" . $SalfStr;
        $encodeStr    = md5($preEncodeStr);

        if ($PostKey == $encodeStr && $ErrCode == '0') {
            return true;
        } else {
            return false;
        }

    }
}


