<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yinggfupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //扫码
        $data = array(
            "pay_memberid"    => $payConf['business_num'],
            "pay_orderid"     => $order,
            "pay_amount"      => $amount,
            "pay_applydate"   => date("Y-m-d H:i:s"), //订单时间,
            "pay_bankcode"    => $bank,
            "pay_notifyurl"   => $ServerUrl,
            "pay_callbackurl" => $returnUrl,
        );

        if ($payConf['pay_way'] == '1') {
            $data['pay_bankcode'] = '907'; //银行编码
        } else {
            $data['pay_bankcode'] = $bank; //银行编码
        }

        $signStr                 = self::getSignStr($data, true,true);
        $sign                    = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'])); //MD5签名
        $data["pay_md5sign"]     = $sign;
        $data['pay_attach']      = $amount;
        $data['pay_productname'] = 'zhif';
        unset($reqData);
        return $data;
    }


    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $returnArray = array( // 返回字段
            "memberid"       => $data["memberid"], // 商户ID
            "orderid"        => $data["orderid"], // 订单号
            "amount"         => $data["amount"], // 交易金额
            "datetime"       => $data["datetime"], // 交易时间
            "transaction_id" => $data["transaction_id"], // 支付流水号
            "returncode"     => $data["returncode"],
        );
        $signStr                 = self::getSignStr($returnArray, true,true);
        $sign                    = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key'])); //MD5签名
        if ($sign == strtoupper($data["sign"]) && $data['returncode'] == '00') {
            return true;
        } else {
            return false;
        }
    }

}