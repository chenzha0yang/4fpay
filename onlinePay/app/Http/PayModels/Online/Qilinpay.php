<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qilinpay extends ApiModel
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

        $price      = sprintf("%.2f", $amount);//订单定价
        $isType     = $bank;//支付类型
        $userId     = $payConf['business_num'];//商户ID
        $apiKey     = $payConf['md5_private_key'];
        $orderUid   = "username";//您的自定义用户ID
        $goodsName  = "zhif";//商品名称
        $orderId    = $order;//您的自定义单号

        $key = md5($userId . $apiKey . $isType . $price . $orderId . $orderUid . $goodsName . $returnUrl . $ServerUrl);

        $data               = [];
        $data['userid']     = $userId;
        $data['key']        = $key;
        $data['istype']     = $isType;
        $data['price']      = $price;
        $data['orderid']    = $orderId;
        $data['orderuid']   = $orderUid;
        $data['goodsname']  = $goodsName;
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;

        unset($reqData);
        return $data;
    }
}