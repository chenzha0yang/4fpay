<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jishipay extends ApiModel
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

        //TODO: do something
        self::$method = 'post';

        $data = [];
        $data['partner'] = $payConf['public_key'];          //PID
        $data['user_seller'] = $payConf['business_num'];    //商户号
        $data['out_order_no'] = $order;                     //订单
        $data['subject'] = 'apple';                         //商品描述
        $data['total_fee'] = $amount;
        $data['body'] = 'body';                             //订单描述
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);
        if ( $payConf['pay_way'] == "1") {
            $data['pay_type'] = "wangyin";                  //请求类型
        } else {
            $data['pay_type'] = $bank;                      //请求类型
        }
        $data['banktype'] = $bank;                          //银行卡类型
        unset($reqData);
        return $data;
    }
}