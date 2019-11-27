<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yuanpay extends ApiModel
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

        $data       = [];
        $data['business_code']  = $payConf['business_num'];//商户ID
        $data['business_order'] = $order;//商户订单号
        $data['flow_balance']   = $amount;//订单金额，单位为RMB元
        $data['flow_description'] = 'name';//商品名
        $data['create_time'] = date('YmdHis');//交易时间
        $data['pay_channel'] = $bank;//支付方式
        $data['return_url']  = $returnUrl;//同步地址
        $data['notify_url']  = $ServerUrl;//异步通知信息
        $data['attach'] = $order;
        $data['device'] = '1';
        $data['sign'] = self::buildRequestPara($data,$payConf);

        unset($reqData);
        return $data;
    }

    private static function buildRequestPara($para_temp,$payconf) {
        $para_s = self::paraFilter($para_temp);//对待签名参数进行过滤
        $sign = self::buildRequestMysign($para_s,$payconf);//生成签名结果，并与签名方式加入请求提交参数组中
        return $sign;
    }

    private static function paraFilter($para) {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $val == "")continue;
            else    $para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }

    private static function buildRequestMysign($para_s,$payconf) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = self::createLinkstring($para_s);
        // print $prestr;die;
        $mysign = self::md5Sign($prestr,$payconf['public_key'],$payconf['md5_private_key']);;
        return $mysign;
    }

    private static function createLinkstring($para) {
        $arg  = "";
        while (list ($key, $val) = each ($para)) {
            $arg.=$val;
        }

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()){
            $arg = stripslashes($arg);
        }
        return $arg;
    }

    private static function md5Sign($prestr,$key,$secret) {
        $prestr = $prestr.$key.$secret;
        return strtolower(md5($prestr));
    }

}