<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jiufuvpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        //TODO: do something

        $data['merchant_id'] = $payConf['business_num'];
        $data['payment_way'] = $bank;
        if ($payConf['pay_way'] == 0) {
            $data['payment_way'] = '30';
            $data['bank_code']   = $bank;
        }
        $data['order_amount']    = sprintf('%2f', $amount);
        $data['source_order_id'] = $order;
        $data['goods_name']      = 'goodsName';
        $data['client_ip']       = self::getIp();
        $data['notify_url']      = $ServerUrl;
        $data['return_url']      = $returnUrl;
        $data['sign']            = self::signature($payConf['md5_private_key'], $data);
        unset($reqData);
        return $data;
    }

    /**
     * 计算参数签名
     * @param string $key
     * @param array  $params
     * @return string
     */
    public static function signature($key = '', $params = [])
    {
        $params['token'] = $key; //加入token
        ksort($params); //参数数组按键升序排列
        $clear_text = '';    //将参数值按顺序拼接成字符串
        foreach ($params as $key => $value) {
            $clear_text .= $key . '=' . $value . '&';
        }
        $clear_text  = trim($clear_text, '&');
        $cipher_text = md5($clear_text); //计算md5 hash
        return $cipher_text;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $mySign = self::signature($payConf['md5_private_key'], $data);
        if($sign == $mySign && $data['status'] == '1'){
            return true;
        } else {
            return false;
        }
    }
}