<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yishipay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        $data = array(

            'paytype'    => $bank,                            //支付方式
            'orderno'    => $order,                           //商户订单号
            'notify_url' => $ServerUrl,                       //异步通知地址
            'return_url' => $returnUrl,                       //同步通知地址
            'goodsname'  => 'dulex',                          //商品名称
            'price'      => sprintf('%.2f', $amount),  //金额
            'attach'     => 'test',                           //附加内容
            'orderuid'   => $payConf['business_num'],         //自定义客户号
        );
        ksort($data);
        $signStr = '';

        foreach ($data as $val) {
            $signStr .= $val;
        }
        $data['uid'] = $payConf['business_num'];
        $data['sign'] = md5($signStr . $payConf['md5_private_key'] . $data['uid']);
        unset($reqData);
        return $data;
    }


}