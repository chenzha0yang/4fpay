<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yikuaipay extends ApiModel
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

        $data                    = [];
        $data['p0_Cmd']          = "Buy";
        $data['p1_MerId']        = $payConf['business_num'];
        $data['p2_Order']        = $order;//订单号
        $data['p3_Amt']          = sprintf("%1\$.2f", $amount);//支付金额
        $data['p4_Cur']          = "CNY";//交易币种,固定值"CNY".
        $data['p5_Pid']          = 'asd';//商品名称
        $data['p6_Pcat']         = 'ssd';//商品种类
        $data['p7_Pdesc']        = 'qsd';//商品描述
        $data['p8_Url']          = $ServerUrl;//
        $data['pa_MP']           = 'adda';//商户扩展信息
        $data['pd_FrpId']        = $bank;//支付通道编码,即银行卡
        $data['pr_NeedResponse'] = "1";//应答机制  默认为"1": 需要应答机制;
        $hmac                    = self::HmacMd5($data, $payConf['md5_private_key']);
        $data['hmac']            = $hmac;

        unset($reqData);
        return $data;
    }

    private static function HmacMd5($data, $key)
    {
        ksort($data);
        $sbOld = "";
        foreach ($data as $value) {
            $sbOld .= $value;
        }
        $key   = iconv("GB2312", "UTF-8", $key);
        $sbOld = iconv("GB2312", "UTF-8", $sbOld);
        $b     = 64;
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key   = str_pad($key, $b, chr(0x00));
        $ipad  = str_pad('', $b, chr(0x36));
        $opad  = str_pad('', $b, chr(0x5c));
        $kipad = $key ^ $ipad;
        $kopad = $key ^ $opad;
        return md5($kopad . pack("H*", md5($kipad . $sbOld)));
    }
}