<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Haoftpay extends ApiModel
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

        //TODO: do something
        if ($payConf['pay_way'] == '1') {
            $bank = '907';
        }
        $data = array(
            'pay_memberid'    => $payConf['business_num'], //商户编号
            'pay_orderid'     => $order, //订单号
            'pay_applydate'   => date('Y-m-d H:i:s'), //提交时间
            'pay_bankcode'    => $bank, //银行编码
            'pay_notifyurl'   => $ServerUrl, //服务端通知
            'pay_callbackurl' => $returnUrl, //页面跳转通知
            'pay_amount'      => sprintf('%.2f',$amount), //订单金额，单笔需小于5000，并且不能是100的整数倍，微信
        );
        $str = self::getSignStr($data,true,true);
        $sign = strtoupper(md5($str . '&key=' . $payConf['md5_private_key']));
        $data['pay_md5sign']     = $sign;
        $data['pay_productname'] = 'productname';
        unset($reqData);
        return $data;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $datasign = $data['sign'];
        unset($data['sign']);
        unset($data['attach']);
        $str = self::getSignStr($data,true,true);
        $sign = strtoupper(md5($str . '&key=' . $payConf['md5_private_key']));
        if ($sign == $datasign && $data['returncode'] == '00') {
            return true;
        } else {
            return false;
        }

    }

}