<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Mashangzfpay extends ApiModel
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
        $data['version']    = '1.0'; //版本号
        $data['customerid'] = $payConf['business_num']; //商户编号
        $data['sdorderno']  = $order; //商户订单号
        $data['total_fee']  = sprintf("%.2f",$amount);//订单金额
        if ((int)$payConf['pay_way'] === 1) {
            $data['bankcode'] = $bank; //银行编号
            $data['paytype']  = 'bank'; //支付方式
        } else {
            $data['paytype'] = $bank; //支付方式
        }
        $data['notifyurl'] = $ServerUrl; //异步通知URL
        $data['returnurl'] = $returnUrl; //同步跳转URL
        $data['sign'] = md5("customerid=".$data['customerid']."&paytype=".$data['paytype']."&sdorderno=".$data['sdorderno']."&total_fee=".$data['total_fee']."&version=".$data['version']."&".$payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        $signStr="customerid=".$data['customerid']."&paytype=".$data['paytype']."&sdorderno=".$data['sdorderno']."&sdpayno=".$data['sdpayno']."&status=".$data['status']."&total_fee=".$data['total_fee'];
        $signTrue = md5($signStr . "&".$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }

}