<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jutongtwopay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something

        $data                = [];
        $data['return_type'] = 'html';
        $data['api_code']    = $payConf['business_num'];
        $data['is_type']     = 'wechat';
        $data['price']       = sprintf('%.2f', $amount);
        $data['order_id']    = $order;
        $data['time']        = time();
        $data['mark']        = 'goodsName';
        $data['return_url']  = $returnUrl;
        $data['notify_url']  = $ServerUrl;


        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign     = $data['sign'];
        $signdata = array(
            'api_code'   => $payConf['business_num'],            //商户的id;
            'paysapi_id' => $data['paysapi_id'],        //服务器API接口返回的唯一支付编码ID
            'order_id'   => $data['order_id'],            //用户订单编号ID
            'is_type'    => $data['is_type'],                //支付类型
            'price'      => $data['price'],                    //订单金额
            'real_price' => $data['real_price'],        //实际支付金额
            'mark'       => $data['mark'],                    //此处填写产品名称，或充值，消费说明
            'code'       => $data['code']                        //订单状态
        );
        $signStr      = self::getSignStr($signdata, true, true);
        $mySign = strtoupper(md5($signStr . '&key=' . $payConf['md5_private_key']));
        if ($sign == $mySign && $data['code'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}