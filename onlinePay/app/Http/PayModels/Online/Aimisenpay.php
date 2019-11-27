<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Aimisenpay extends ApiModel
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
        self::$resType = 'other';
        self::$httpBuildQuery = true;

        $payInfo = explode('@', $payConf['business_num']);

        if(!isset($payInfo[1])){
            echo '绑定格式有误！请参照：商户号@商户唯一标识';exit();
        }

        if ((int)$payConf['pay_way'] === 1) {
            $data['extend'] = json_encode(['bankName' => $bank, 'cardType' => '借记卡']);
            $bank           = '80103';
        }
        $data['src_code']     = $payInfo[1];//    商户唯一标识
        $data['out_trade_no'] = $order;//    商户交易订单号
        $data['total_fee']    = $amount * 100;//    订单总金额，单位分
        $data['time_start']   = date('YmdHis');//   发起交易的时间
        $data['goods_name']   = $order;//    商品名称
        $data['trade_type']   = $bank;//交易类型：微信扫码:50104;支付宝扫码:60104;
        $data['finish_url']   = $ServerUrl;//   支付完成页面的url
        $data['mchid']        = $payInfo[0];//   商户号
        ksort($data);
        $signStr = "";
        foreach ($data as $key => $value) {
            $signStr .= $key . "=" . $value . "&";
        }
        $data['sign'] = strtoupper(md5($signStr . "key=" . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['respcd'] == '0000') {
            $data['qrCode'] = $data["data"]["pay_params"];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['total_fee'])) {
            $arr['total_fee'] = $arr['total_fee'] / 100;
        }
        return $arr;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $signStr = "";
        foreach ($data as $key => $value) {
            if ($value && $key != 'sign') {
                $signStr = $signStr . $key . "=" . $value . "&";
            }
        }
        $signTrue = strtoupper(md5($signStr . "key=" . $payConf['md5_private_key']));
        if (strtoupper($sign) == strtoupper($signTrue) && trim($data['order_status']) == "3") {
            return true;
        }
        return false;
    }


}