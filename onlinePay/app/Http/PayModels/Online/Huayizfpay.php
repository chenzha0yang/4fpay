<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huayizfpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

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
        $data['account_id'] = $payConf['business_num'];
        $data['content_type'] = 'text';
        $data['thoroughfare'] = 'alipay_auto';
        $data['type'] = 2;
        $data['out_trade_no'] = $order;
        $data['robin'] = 2;
        $data['keyId'] = '';
        $data['amount'] = sprintf("%.2f", $amount);
        $data['callback_url'] = $ServerUrl;
        $data['success_url'] = $returnUrl;
        $data['error_url'] = $returnUrl;

        $sign = self::sign($payConf['md5_private_key'],$data['amount'],$data['out_trade_no']);
        $data['sign'] = $sign;

        unset($reqData);
        return $data;
    }


    public static function sign ($key_id, $amount,$out_trade_no)
    {
        $data = md5($amount . $out_trade_no);
        $key[] ="";
        $box[] ="";
        $cipher = '';
        $pwd_length = strlen($key_id);
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++)
        {
            $key[$i] = ord($key_id[$i % $pwd_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++)
        {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $data_length; $i++)
        {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }
        return md5($cipher);
    }

    public static function signOther($mod, $data, $payConf)
    {
        $sign = $data['sign'];
        $mySign = self::sign($payConf['md5_private_key'],$data['amount'],$data['out_trade_no']);
        if (strtoupper($sign) == strtoupper($mySign)) {
            return true;
        }
        return false;
    }
}