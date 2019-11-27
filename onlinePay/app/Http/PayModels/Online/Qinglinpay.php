<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qinglinpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        $data = array(
            'return_type' => 'Json', //用户订单编号ID
            'api_code'    => $payConf['business_num'], //此处填写商户的id
            'is_type'     => $bank, //支付渠道
            'price'       => sprintf("%.2f", $amount), //支付金额
            'order_id'    => $order, //订单号
            'time'        => time(), //支付时间
            'mark'        => 'mark', //此处填写产品名称，或充值，消费说明
            'return_url'  => $returnUrl, //支付成功，用户会跳转到这个地址
            'notify_url'  => $ServerUrl, //通知异步回调接收地址
        );

        $buff                 = self::getSignStr($data, true, true);
        $data['sign']     = strtoupper(md5($buff . "&key=" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        $data['order_id'] = $res['order_id'];
        $data['price'] = $res['price'];
        return $data;
    }

    public static function SignOther($type, $post, $payConf)
    {
        $post = file_get_contents('php://input');
        $json_arr  = json_decode(stripslashes($post), true);
        $signature = $json_arr["sign"];
        $signdata  = array(
            'api_code'   => $payConf['business_num'], //商户的id;
            'paysapi_id' => $json_arr["paysapi_id"], //服务器API接口返回的唯一支付编码ID
            'order_id'   => $json_arr["order_id"], //用户订单编号ID
            'is_type'    => $json_arr["is_type"], //支付类型
            'price'      => $json_arr["price"], //订单金额
            'real_price' => $json_arr["real_price"], //实际支付金额
            'mark'       => $json_arr["mark"], //此处填写产品名称，或充值，消费说明
            'code'       => $json_arr["code"], //订单状态
        );
        $buff = self::getSignStr($signdata, true, true);
        $sign = strtoupper(md5($buff . "&key=" . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signature)  && $json_arr['code'] == '1') {
            return true;
        }
        return false;
    }


}