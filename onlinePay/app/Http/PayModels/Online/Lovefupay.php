<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Lovefupay extends ApiModel
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
        //TODO: do something

        if ($payConf['pay_way'] <> '1' && $payConf['is_app'] <> 1) {
            self::$reqType        = 'curl';
            self::$payWay         = $payConf['pay_way'];
            self::$httpBuildQuery = true;
        }

        $data = array(
            'version'      => 'v1', //版本号
            'merchant_no'  => $payConf['business_num'], //商户号
            'order_no'     => $order, //商户订单号
            'goods_name'   => base64_encode('goodsname'), //商品名称
            'order_amount' => sprintf('%.2f', $amount), //订单金额
            'backend_url'  => $ServerUrl, //下行异步通知地址
            'frontend_url' => $returnUrl, //商户的支付结果显示页面地址
            'reserve'      => '',
            'bank_code'    => $bank, //银行编号
            'card_type'    => '0', //允许支付的卡类型
        );
        if ($payConf['pay_way'] == '1') {
            $data['pay_mode'] = '01';
        } elseif ($payConf['is_app'] == '1') {
            $data['pay_mode'] = '12';
        } else {
            $data['pay_mode'] = '09';
        }
        $string       = "version=" . $data['version'] . "&merchant_no=" . $data['merchant_no'] . "&order_no=" . $data['order_no'] . "&goods_name=" . $data['goods_name'] . "&order_amount=" . $data['order_amount'] . "&backend_url=" . $data['backend_url'] . "&frontend_url=" . $data['frontend_url'] . "&reserve=" . $data['reserve'] . "&pay_mode=" . $data['pay_mode'] . "&bank_code=" . $data['bank_code'] . "&card_type=" . $data['card_type'];
        $sign         = md5($string . '&key=' . $payConf['md5_private_key']);
        $data['sign'] = $sign; //签名
        unset($reqData);
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $datasign = $data['sign'];
        $md5str   = 'merchant_no=' . $data['merchant_no'] . '&order_no=' . $data['order_no'] . '&order_amount=' . $data['order_amount'] . '&original_amount=' . $data['original_amount']
            . '&upstream_settle=' . $data['upstream_settle'] . '&result=' . $data['result'] . '&pay_time=' . $data['pay_time'] . '&trace_id=' . $data['trace_id'] . '&reserve=' . $data['reserve'];
        $sign     = md5($md5str . '&key=' . $payConf['md5_private_key']);
        if ($sign == $datasign && $data['result'] == 'S') {
            return true;
        } else {
            return false;
        }
    }

}