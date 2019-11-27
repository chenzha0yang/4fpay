<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinyizfpay extends ApiModel
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
     * @internal param null|string $user
     */
    public static function getAllInfo ($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        self ::$reqType = 'curl';
        self ::$payWay = $payConf['pay_way'];
        self ::$unit = 2; //单位： 分
        self ::$httpBuildQuery = true;
        if ( $payConf['is_app'] == 1 ) {
            self ::$isAPP = true;
        }
        $data = array(
            'assCode' => $payConf['business_num'],  //商户号
            'assPayOrderNo' => $order,                    //订单号
            'assPayMoney' => $amount * 100,             //金额
            'assNotifyUrl' => $ServerUrl,                //异步通知地址
            'assReturnUrl' => $returnUrl,                //同步通知地址
            'assCancelUrl' => $returnUrl,                //取消地址
            'paymentType' => $bank,                     //支付类型
            'subPayCode' => '',                        //子支付类型，银行
            'assPayMessage' => '',                        //订单标题
            'assGoodsDesc' => '',                        //订单描述
        );
        if ( $payConf['pay_way'] != 1 ) {
            $data['subPayCode'] = $bank;
        } else {
            $data['paymentType'] = 'gate_web_direct';
        }
        $signStr = '&' . self ::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult ($request)
    {
        $data = $request -> all();
        $data['assPayMoney'] = $data['assPayMoney'] / 100;
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        if ($data['respCode'] == '60006') {
            $data['respMsg'] = '支付成功';
        }

        $signStr = '&' . self ::getSignStr($data, true, true);
        $mySign = strtoupper(md5($signStr . $payConf['md5_private_key']));
        if ($sign == $mySign) {
            return true;
        }
        return false;
    }
}
