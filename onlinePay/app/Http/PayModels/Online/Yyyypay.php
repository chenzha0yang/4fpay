<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yyyypay extends ApiModel
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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地
        $data['app_id'] = $payConf['business_num'];       //商户号
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        if ((int)$payConf['pay_way'] === 1) {
            $data['card_type'] = 1;
            $data['bank_code'] = $bank;
            $data['pay_type'] = 12;
        } else {
            $data['pay_type'] = $bank;
        }
        $data['order_id'] = $order;//商户订单号
        $data['order_amt'] = sprintf('%.2f', $amount);//订单金额
        $data['notify_url'] = $ServerUrl;//异步通知
        $data['return_url'] = $returnUrl;//同步通知
        $data['time_stamp'] = date("YmdHis", time());//时间戳
        $key = md5($payConf['md5_private_key']);
        $data['sign'] = md5("app_id={$data['app_id']}&pay_type={$data['pay_type']}&order_id={$data['order_id']}&order_amt={$data['order_amt']}&notify_url={$data['notify_url']}&return_url={$data['return_url']}&time_stamp={$data['time_stamp']}&key={$key}");
        $data['goods_name'] = 'ipad';//商品名称
        $data['user_ip'] = self::getIp();//用户ip

        unset($reqData);
        return $data;
    }
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $res = self::getSignStr($data, true, true);
        $signTrue = md5($res . "&key=" . md5($payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue) && $data['pay_result'] == '20') {
            return true;
        }
        return false;
    }

}