<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Lixiangpay extends ApiModel
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
        self::$unit = 2; // 单位 ： 分

        $data['tradeType']  = 'pay.submit'; //交易类型
        $data['channel']    = $bank; //支付渠道
        $data['mchNo']      = $payConf['business_num']; //商户号
        $data['mchOrderNo'] = $order; //订单号
        $data['amount']     = (int)$amount * 100; //金额
        $data['timePaid']   = date("YmdHis"); //请求时间
        $data['bankType']   = '';
        if ($payConf['pay_way'] == 0) {
            $data['channel']  = 'union_pay'; //网银
            $data['bankType'] = $bank;
        }
        $data['notifyUrl']   = $ServerUrl; //后台通知地址
        $signStr             = self::getSignStr($data, false, true); //请求参数排序拼接
        $sign                = strtoupper(md5($signStr . '&paySecret=' . $payConf['md5_private_key']));
        $data['version']     = '1.0'; //版本号
        $data['currency']    = 'CNY'; //货币类型
        $data['callbackUrl'] = $returnUrl; //页面返回地址
        $data['remark']      = 'lx'; //支付描述
        $data['goodsDesc']   = 'lx';
        $data['sign']        = $sign;
        unset($reqData);
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['amount'])) {
            $arr['amount'] = $arr['amount'] / 100;
        }

        return $arr;
    }

    public static function SignOther($model, $data, $payConf)
    {

        $sign   = $data['sign'];
        $status = $data['status'];
        unset($data['sign']);
        unset($data['errMsg']);
        unset($data['message']);
        unset($data['remark']);
        unset($data['status']);
        $signStr = self::getSignStr($data, false, true);
        $mysgin  = strtoupper(md5($signStr . "&paySecret=" . $payConf['md5_private_key']));
        if ($mysgin == strtoupper($sign) && $status == 0) {
            return true;
        }
        return false;
    }

}