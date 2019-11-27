<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinyidaopay extends ApiModel
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
        $data['version'] = '1.0.1';//版本号
        $data['subject'] = 'sub';//商品标题
        $data['amount'] = $amount;//订单金额
        $data['notifyUrl'] = $ServerUrl;//异步通知地址
        $data['extra_para'] = $order;
        $data['orgOrderNo'] = $order;//订单编号
        $data['returnUrl'] = $returnUrl;//同步通知地址
        $data['tranTp'] = '0';//通道类型,固定值：0
        if ($payConf['pay_way'] == '1') {
            $data['merchantCode'] = $payConf['business_num'];//商户编号
            $data['num'] = '1';
            $data['desc'] = '1';
            $data['bankWay'] = $bank;//付款方式
        } else {
            $data['source'] = $bank;//付款方式
        }
        $postData = array();
        if ($payConf['pay_way'] == '1') {
            $postData = self::sign($data, $payConf);
            $postData['act'] = 'postsub';
        } else {
            $postData['reqStr'] = self::sign($data, $payConf);
            $postData['payway'] = $payConf['rsa_private_key'];
            $postData['act'] = 'Xinyxpay';
        }
        unset($reqData);
        return $data;
    }

    private static function sign($arr, $payconf)
    {
        $str = self::sortData($arr);
        $baseStr = base64_encode($str);
        $aesPrivage = self::encrypt($baseStr, $payconf['rsa_private_key']);
        $aesPrivage = strtoupper($aesPrivage);
        $sign = strtoupper(md5($aesPrivage . $payconf['public_key']));
        $arr['sign'] = $sign;
        $str2 = self::sortData($arr);
        $baseStr2 = base64_encode($str2);
        $transData = self::encrypt($baseStr2, $payconf['rsa_private_key']);
        $jsonData = array();
        $jsonData['merchantCode'] = $payconf['pay_id'];//商户编号
        $jsonData['transData'] = $transData;
        if ($payconf['pay_way'] == '0') {
            $jsonData['extra_para'] = $arr['extra_para'];
            return $jsonData;
        } else {
            $reqStr = "reqJson=" . json_encode($jsonData);
            return $reqStr;
        }

    }

    /**
     * 排序
     * @param $arr
     * @return mixed|string
     */
    private static function sortData($arr)
    {
        array_walk($arr, function (&$v) {
            if (is_array($v)) {
                array_walk_recursive($v, function (&$v1) {
                    if (is_object($v1)) {
                        $v1 = get_object_vars($v1);
                        ksort($v1);
                    }
                });
                ksort($v);
            }
        });

        ksort($arr);
        key($arr);
        $str = "";
        foreach (array_keys($arr) as $key) {
            $str .= $key . "=" . $arr[$key] . "&";
        }
        $str = rtrim($str, "&");
        return $str;
    }

    /**
     * 加密
     * @param String input 加密的字符串
     * @param String key   解密的key
     * @return HexString
     */
    private static function encrypt($input, $key)
    {

        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = bin2hex($data);
        return $data;

    }

    /**
     * 填充方式 pkcs5
     * @param String text        原始字符串
     * @param String blocksize   加密长度
     * @return String
     */
    private static function pkcs5_pad($text, $blocksize)
    {

        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);

    }

}