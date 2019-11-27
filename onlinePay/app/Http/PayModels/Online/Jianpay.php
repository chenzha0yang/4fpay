<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Jianpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        $data = array(
            "mchid"           => $payConf['business_num'],  //商户号
            "price"         => $amount,                   //金额
            "out_order_id"  => $order,                    //订单号
            "type"          => $bank,                     //支付方式
            "notifyurl"    => $ServerUrl,                //异步通知地址
        );

        $str  = self::getSignStr($data, true ,true);
        //去掉&字符
        $signStr = str_replace('&','',$str);
        //拼接商户密钥
        $data['sign'] = strtolower(md5(md5($signStr).'W7KuKR9UyHKkRN10us2DTYT770AZKl95'));

        unset($reqData);
        return $data;
    }

    /**
     * 拼接数组的 value 值 按照参数名ASCII字典序排序
     * @param array $para
     * @param bool $isNull
     * @param bool $sort
     * @param string $space
     * @return bool|string
     */
    public static function getSignStr($para, $isNull = true, $sort = false, $space = '&')
    {
        if ($sort) {
            ksort($para);
        }

        $arg  = "";
        foreach ($para as $key => $value) {
            if($isNull) {
                if($value != '' && $value != null){
                    $arg .= "{$value}{$space}";
                }
            } else {
                $arg .= "{$value}{$space}";
            }
        }

        //去掉最后一个&字符
        $arg = rtrim($arg, $space);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

}