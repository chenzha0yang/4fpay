<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Azzhifupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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

        $data['p0_Cmd']          = 'Buy';
        $data['p1_MerId']        = $payConf['business_num'];
        $data['p2_Order']        = $order;
        $data['p3_Amt']          = $amount;
        $data['p4_Cur']          = 'CNY';
        $data['p8_Url']          = $ServerUrl;
        $data['p9_SAF']          = '0';
        $data['pd_FrpId']        = $bank;
        $data['pr_NeedResponse'] = '1';
        $data['pageUrl']         = $returnUrl;
        $signStr                 = $data['p0_Cmd'] . $data['p1_MerId'] . $data['p2_Order'] . $data['p3_Amt'] . $data['p4_Cur'] . $data['p8_Url'] . $data['p9_SAF'] . $data['pd_FrpId'] . $data['pr_NeedResponse'];
        $data['hmac']            = self::HmacMd5($signStr, $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }


    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign     = $data['hmac'];
        $signStr  = $data['p1_MerId'] . $data['r0_Cmd'] . $data['r1_Code'] . $data['r2_TrxId'] . $data['r3_Amt'] . $data['r4_Cur'] . $data['r5_Pid'] . $data['r6_Order'] . $data['r7_Uid'] . $data['r8_MP'] . $data['r9_BType'];
        $signTrue = self::HmacMd5($signStr, $payConf['md5_private_key']);
        if ($sign == $signTrue && $data['r1_Code'] == '1') {
            return true;
        } else {
            return false;
        }
    }

    public static function HmacMd5($data, $key)
    {
        $key  = iconv("GB2312", "UTF-8", $key);
        $data = iconv("GB2312", "UTF-8", $data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }
        $key    = str_pad($key, $b, chr(0x00));
        $ipad   = str_pad('', $b, chr(0x36));
        $opad   = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

}