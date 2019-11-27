<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tianzepay extends ApiModel
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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        $data = [];
        $data["p0_Cmd"] = 'Buy';//用户名
        $data["p1_MerId"] = $payConf['business_num'];//商户ID
        $data["p2_Order"] = $order;//商户秘钥
        $data["p3_Amt"] = sprintf('%.2f', $amount);
        $data["p4_Cur"] = 'CNY';//weixin,alipay
        $data["p5_Pid"] = '';
        $data["p6_Pcat"] = '';
        $data["p7_Pdesc"] = '';
        $data["p8_Url"] = $ServerUrl;
        $data["p9_SAF"] = '';
        $data["pa_MP"] = '';
        $data["pd_FrpId"] = '';
        $data["pr_NeedResponse"] = '';

        $string = '';
        foreach ($data as $key => $value) {
            $string .= $value;
        }

        $sign = self::HmacMd5($string, $payConf['md5_private_key']);
        $data['hmac'] = $sign; //签名
        unset($reqData);
        return $data;
    }

    private static function HmacMd5($data, $key)
    {
        $key = iconv("GB2312", "UTF-8", $key);
        $data = iconv("GB2312", "UTF-8", $data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

}