<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tianhezfpay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
//        self::$httpBuildQuery = true;

        $data = array(
            'mer_id'       => $payConf['business_num'],
            'title'        => 'name',
            'money'        => $amount * 100,
            'out_trade_no' => $order,
            'trade_type'   => $bank,
            'create_ip'    => self::getClientIP(),
            'notify_url'   => $ServerUrl,
            'callback_url' => $returnUrl,
            'remark'       => 'remark',
        );

        $signStr      = 'mer_id=' . $data['mer_id'] . '&money=' . $data['money'] . '&out_trade_no=' . $data['out_trade_no'] . '&trade_type=' . $data['trade_type'];
        $data['sign'] = strtoupper(md5($signStr . '&' . $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * @return array|false|string
     * 获取客户端真实IP
     */
    public static function getClientIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }
}