<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Dexipay extends ApiModel
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

        if($payConf['is_app'] == 1){
            self::$isAPP = true;
        }
        $data = array(
            'account_id'    => $payConf['business_num'],   //商户号
            'content_type'  => 'text',
            'thoroughfare'  => 'service_auto',
            'type'          => $bank,
            'out_trade_no'  => $order,
            'robin'         => 2,
            'amount'        => sprintf('%.2f',$amount),
            'callback_url'  => $ServerUrl,
            'success_url'   => $returnUrl,
            'error_url'     => $returnUrl,
        );
        $data['sign']  = self::sign($payConf['md5_private_key'],$data);

        unset($reqData);
        return $data;
    }

    /**
     * 签名
     * @param $key_id
     * @param $array
     * @return string
     */
    public static function sign ($key_id, $array)
    {
        $data = md5(number_format($array['amount'],2) . $array['out_trade_no']);
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

    /**
     * 回调验签
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf){
        $sign = self::sign($payConf['md5_private_key'],$data);
        if($data['status'] == 'success'){
            if($data['account_key'] != $payConf['md5_private_key']){
                return false;
            }
            if($sign !== $data['sign']){
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

}