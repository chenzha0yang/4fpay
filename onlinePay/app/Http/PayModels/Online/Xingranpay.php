<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xingranpay extends ApiModel
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

        self::$unit         = 2;
        $data               = [];
        $data['partnerId']    = $payConf['business_num'];
        $data['servicetype']  = "Buy";
        $data['orderno']      = $order;
        $data['amount']       = $amount * 100;
        $data['currency']     = "CNY";
        $data['notify']       = $ServerUrl;
        $data['type']         = $bank;
        $data['needresponse'] = 1;

        $str  = $data['servicetype'] . $data['partnerId'] . $data['orderno'] . $data['amount'] . $data['currency'] . $data['notify'] . $data['type'] . $data['needresponse'];
        $hmac = self::hmacMD5($str, $payConf['md5_private_key']);
        $data['hmac'] = $hmac;

        unset($reqData);
        return $data;
    }

    public static  function HmacMd5($data,$key)
    {
        // RFC 2104 HMAC implementation for php.
        // Creates an md5 HMAC.
        // Eliminates the need to install mhash to compute a HMAC
        // Hacked by Lance Rushing(NOTE: Hacked means written)

        //需要配置环境支持iconv，否则中文参数不能正常处理
        $key = iconv("GB2312","UTF-8",$key);
        $data = iconv("GB2312","UTF-8",$data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack("H*",md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad ;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack("H*",md5($k_ipad . $data)));
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['hmac'];
        $signStr = "{$data['orderno']}{$data['state']}{$data['amunt']}{$data['orderid']}{$data['date']}";
        $hmac = self::hmacMD5($signStr, $payConf['md5_private_key']);
        if ( $sign == $hmac && $data['state'] == "1" ) {
            return true;
        } else {
            return false;
        }
    }

}