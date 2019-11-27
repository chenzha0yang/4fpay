<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huangjinpay extends ApiModel
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

        self::$unit       = 2;
        $data             = array();
        $data['appid']    = $payConf['business_num']; //商户应用号
        $data['amount']   = $amount * 100; //支付金额  单位 分
        $data['order_id'] = $order; //应用订单号
        $data['bank']     = explode("_", $bank)[0]; //支付平台
        $data['action']   = explode("_", $bank)[1]; //支付方式
        $data['notify']   = $ServerUrl; //回调地址
        $data['notice']   = $returnUrl; //页面跳转
        $data['remark']   = 'chongzhi'; //商户备注
        $data['ip']       = $_SERVER['SERVER_ADDR']; //用户IP
        $data['mobile']   = 0; //是否手机端
        $data['ua']       = ''; //浏览器UA
        $data['username'] = get_session('username'); //玩家用户名

        $strToSign      = self::getSignStr($data, false, true);
        $data['sign']   = self::getMd5Sign($strToSign, $payConf['md5_private_key']); //签名

        unset($reqData);
        return $data;
    }
}