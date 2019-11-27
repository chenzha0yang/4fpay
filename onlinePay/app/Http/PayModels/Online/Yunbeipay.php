<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yunbeipay extends ApiModel
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

        self::$unit          = 2;
        $data                = [];
        $data['companyId']   = $payConf['business_num'];//用户ID 由商务分配
        $data['userOrderId'] = $order;//用户自定义订单同步时候会返回
        $data['payType']     = $bank;//支付方式
        $data['item']        = 'zhif';//商品名
        $data['fee']         = $amount * 100;//价格 (单位分)
        $data['callbackUrl'] = $returnUrl;//前端回调地址(不是所有通道都能用)
        $data['syncUrl']     = $ServerUrl;//异步通知地址
        $data['ip']          = self::getip();//用户的IP
        if ($payConf['pay_way'] == '9') {
            $data['mobile'] = self::getmobile();
        }
        $sign         = MD5($data['companyId'] . '_' . $data['userOrderId'] . '_' . $data['fee'] . '_' . $payConf['md5_private_key']);
        $data['sign'] = $sign;//签名

        unset($reqData);
        return $data;
    }

    private static function getip()
    {

        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);

        return $cip;
    }

    private static function getmobile()
    {
        $arr = array(
            130, 131, 132, 133, 134, 135, 136, 137, 138, 139,
            144, 147,
            150, 151, 152, 153, 155, 156, 157, 158, 159,
            176, 177, 178,
            180, 181, 182, 183, 184, 185, 186, 187, 188, 189,
        );

        return $arr[array_rand($arr)] . mt_rand(1000, 9999) . mt_rand(1000, 9999);;
    }
}