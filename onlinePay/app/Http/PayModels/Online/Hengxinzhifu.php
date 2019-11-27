<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Hengxinzhifu extends ApiModel
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

        $data       = array(
            'version'     => '3.0', //版本号
            'method'      => 'hxapp.online.interface', //接口名称
            'partner'     => $payConf['business_num'], //商户ID
            'banktype'    => $bank, //银行类型
            'paymoney'    => $amount, //金额
            'ordernumber' => $order, //商户订单号
            'callbackurl' => $ServerUrl, //下行异步通知地址
        );
        $str = self::getSignStr($data, true, true);
        $data['sign']         = md5($str . $payConf['md5_private_key']);

        $data['hrefbackurl'] = $returnUrl; //下行同步通知地址
        $data['attach']      = 'attach'; //备注信息
        $data['isshow']      = '1'; //是否显示收银台

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);

        $md5str = 'partner=' . $data['partner'] . '&ordernumber=' . $data['ordernumber'] . '&orderstatus=' . $data['orderstatus'] . '&paymoney=' . $data['paymoney'];
        $signTrue   = md5($md5str . $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($signTrue) && $data['orderstatus'] == 1) {
            return true;
        }
        return false;
    }


}