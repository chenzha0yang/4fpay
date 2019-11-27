<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Weiyingbaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data = array(
            'pay_memberid'    => $payConf['business_num'],   //商户ID
            'pay_orderid'     => $payConf['business_num'],   //订单号
            'pay_applydate'   => date('Y-m-d H:i:s'), //订单时间
            'pay_bankcode'    => $bank,                      //支付类型
            'pay_notifyurl'   => $ServerUrl,                 // 异步通知地址
            'pay_callbackurl' => $returnUrl,                 // 同步通知地址
            'pay_amount'      => $amount,                    // 金额
            'pay_attach'      => 'none info',                // 商户自定义信息
            'pay_productname' => 'any',                      // 商品名称
            'pay_productid'   => '12',                       // 商品ID
        );

        $signStr = self::getSignStr($data,true,true);
        $data['pay_md5sign'] = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

}