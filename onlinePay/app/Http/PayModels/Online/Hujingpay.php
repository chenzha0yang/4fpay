<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hujingpay extends ApiModel
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


        $params     = [
            //商户ID，由支付平台分配
            'merchantId'      => $payConf['business_num'],
            //充值金额,2位小数或正整数
            'money'           => $amount,
            //当前时间戳
            'timestamp'       => time(),
            //商品名称,可选参数
            'goodsName'       => 'test',
            //异步回调接口,此处需要修改为商户自己服务器的接收订单完成接口
            'notifyUrl'       => $ServerUrl,
            //商户唯一订单号
            'merchantOrderId' => $order,
            //用户UID
            'merchantUid'     => rand() . date("YmdHis"),
            //支付类型
            'payType'         => $bank,
            //表单提交返回数据格式
            'resultFormat'    => 'form',
        ];
        $params['sign'] = self::sign($params, $payConf['rsa_private_key']);


        unset($reqData);
        return $params;
    }

    public static function sign($params, $merchant_private_key)
    {
        unset($params['goodsName']);
        unset($params['resultFormat']);
        ksort($params);
        $paramPairs = [];
        foreach ($params as $k => $v) {
            $paramPairs[] = $k . "=" . $v;
        }
        $signStr              = implode("&", $paramPairs);
        $merchant_private_key = openssl_get_privatekey($merchant_private_key);
        $sign_info            = '';
        openssl_sign($signStr, $sign_info, $merchant_private_key, OPENSSL_PKCS1_PADDING);
        $sign = base64_encode($sign_info);
        return $sign;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $signKeys   = ["merchantId", "money", "payAmount", "orderId", "payTime", "merchantOrderId", "merchantUid", "payType"];
        $signParams = [];
        foreach ($signKeys as $key) {
            $signParams[$key] = $data[$key];
        }
        ksort($signParams);
        $signStr   = urldecode(http_build_query($signParams));
        $publickey = openssl_get_publickey($payConf['public_key']);
        $flag      = openssl_verify($signStr, base64_decode($data['sign']), $publickey);
        if ($flag) {
            return true;
        } else {
            return false;
        }
    }

}