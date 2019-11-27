<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;
use App\Http\Models\CallbackMsg;

class Wanzhifupay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';

        $data         = [
            'trade_type'       => $bank,//支付方式
            'mch_id'           => $payConf['business_num'],// 商户号
            'nonce'            => time(),// 随机字符串
            'timestamp'        => time(),//时间戳
            'subject'          => 'wjf',
            'detail'           => 'wjf',
            'out_trade_no'     => $order,
            'total_fee'        => $amount * 100,
            'spbill_create_ip' => '225.225.225.225',
            'timeout'          => '30',
            'notify_url'       => $ServerUrl
        ];
        $signStr      = self::getSignStr($data, false, true);
        $data['sign'] = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));

        $post['data']         = json_encode($data);
        $post['httpHeaders']  = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen(json_encode($data))
        );
        $post['out_trade_no'] = $data['out_trade_no'];
        $post['total_fee']    = $data['total_fee'];

        unset($reqData);
        return $post;
    }

    public static function getVerifyResult($request, $mod)
    {
        $params            = $request->all();
        $order             = $params['out_trade_no'];
        $bankOrder         = PayOrder::getOrderData($order);//根据订单号 获取入款注单数据
        $payConf           = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        $platformPublicKey = openssl_get_publickey($payConf['public_key']);
        if (self::arrange($params, $platformPublicKey)) {
            if ($params['result_code'] == 'SUCCESS') {
                // TODO 通知成功 业务处理
                $data['out_trade_no'] = $params['out_trade_no'];
                $data['total_fee']    = $params['total_fee'] / 100;
                $data['result_code']  = $params['result_code'];
                return $data;
            }
        } else {
            echo trans("success.{$mod}");
            CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);
            exit();
        }

    }

    public static function arrange($params, $platformPublicKey)
    {
        ksort($params);
        $data = "";
        foreach ($params as $key => $value) {
            if ($value === '' || $value == null || $key == 'sign') {
                continue;
            }
            $data .= $key . '=' . $value . '&';
        }

        $data      = preg_replace("/&$/", '', $data);
        $plaintext = md5($data);
        return self::verifySign($plaintext, $params['sign'], $platformPublicKey);
    }

    public static function verifySign($data = '', $sign = '', $publicKey = '')
    {
        if (!is_string($sign) || !is_string($sign)) {
            return false;
        }
        return (bool)openssl_verify(
            $data,
            base64_decode($sign),
            $publicKey,
            OPENSSL_ALGO_SHA256
        );
    }

    /**
     * 特殊处理已经验签， 直接返回true
     *
     * @return bool
     */
    public static function SignOther()
    {
        return true;
    }
}