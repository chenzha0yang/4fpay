<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wanhuipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

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

        $data = [];
        $data['pay_memberid'] = $payConf['business_num']; //平台分配商户号
        $data['pay_orderid'] = $order;      //上送订单号唯一
        $data['pay_applydate'] = date('Y-m-d H:i:s');        //提交时间，时间格式：2016-12-26 18:18:18
        $data['pay_bankcode'] = $bank;      //参考湾汇文档后续说明
        $data['pay_notifyurl'] = $ServerUrl;       // 服务端返回地址.（POST返回数据）
        $data['pay_callbackurl'] = $returnUrl;      //页面跳转返回地址（POST返回数据）
        $data['pay_amount'] = $amount;           //商品金额

        $signStr           = self::getSignStr($data, true, true);
        $data['pay_attach'] = '';           //附加字段
        $data['pay_productname'] = '';      //商品名称
        $data['pay_productnum'] = '';       //商户品数量
        $data['pay_productdesc'] = '';      //商品描述
        $data['pay_producturl'] = '';       //商户链接地址

        $data['pay_md5sign']      = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));

        unset($reqData);
        return $data;
    }
}