<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Yidatwopay extends ApiModel
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

        $data         = array(
            'account_id'   => $payConf['business_num'],
            'content_type' => 'text',
            'thoroughfare' => 'service_auto',
            'type'         => $bank,
            'out_trade_no' => $order,
            'robin'        => 2,
            'keyId'        => '',
            'amount'       => sprintf('%.2f', $amount),
            'callback_url' => $ServerUrl,
            'success_url'  => $returnUrl,
            'error_url'    => 'http://www.baidu.com',
        );
        $sign         = self::sign($payConf['md5_private_key'], $data);
        $data['sign'] = $sign;
        unset($reqData);
        return $data;
    }

    /**
     * 签名算法
     * @param string $key_id S_KEY（商户KEY）
     * @param array  $array 例子：$array = array('amount'=>'1.00','out_trade_no'=>'2018123645787452');
     * @return string
     */
    public static function sign($key_id, $array)
    {
        $data        = md5(sprintf("%.2f", $array['amount']) . $array['out_trade_no']);
        $key[]       = "";
        $box[]       = "";
        $cipher      = '';
        $pwd_length  = strlen($key_id);
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($key_id[$i % $pwd_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $key[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k      = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }
        return md5($cipher);
    }

    public static function SignOther($model, $data, $payConf)
    {
        $key = $data['account_key'];

        $signArr['amount']       = $data['amount'];
        $signArr['out_trade_no'] = $data['out_trade_no'];

        $mySign = self::sign($payConf['md5_private_key'], $signArr);
        if ($key == $payConf['md5_private_key'] && $mySign == $data['sign']) {
            return true;
        } else {
            return false;
        }

    }
}