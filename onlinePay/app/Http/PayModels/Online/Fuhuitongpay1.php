<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Fuhuitongpay1 extends ApiModel
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

        $collectWays = [
            '2' => 'WXH5', // 手机端微信
            '3' => 'ZFBH5', // 手机端支付宝
            '4' => 'QQZF5', // 手机端QQ
            '6' => 'UPH5', // 手机端银联
            '7' => 'JDH5', // 手机端京东
        ];

        if ($payConf['pay_way'] == 1) {
            //PC端网银
            $tranType = '01';
        } elseif ($payConf['pay_way'] == 9) {
            //银联快捷支付
            $tranType = '02';
        } else {
            $tranType = '00';
        }
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
            $tranType = '03';
            //是手机端，支付方式改变
            foreach ($collectWays as $k => $v) {
                if ($k == $payConf['pay_way']) {
                    $bank = $v;
                }
            }
        }

        //TODO: do something
        self::$unit = 2 ; // 单位：分

        $data = array(
            'merCode' => $payConf['business_num'] ,
            'tranNo' => $order ,
            'tranType' => $tranType ,
            'tranAmt' => $amount * 100 ,
            'collectWay' => $bank ,
            'tranTime' => date('YmdHis') ,
            'noticeUrl' => $ServerUrl ,
            'orderDesc' => 'dulex' ,
            'userIP' => self::getClientIP() ,
        );
        $signStr      = self::getSignStr($data, true, true);
        $data['sign'] = md5($signStr . $payConf['md5_private_key']);
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