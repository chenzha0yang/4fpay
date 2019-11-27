<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Youyoupay extends ApiModel
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

        $data                = [];
        $data['merchant']    = $payConf['business_num'];//商户编号
        $data['sn']          = $order;//流水号
        $data['busiType']    = '100001';//接口类型业务类型，编码为100001
        $data['totalAmount'] = $amount;//交易金额
        $data['subject']     = 'shuoming';//货物说明
        $data['callBack']    = $ServerUrl;//回调通知链接
        $data['channel']     = $bank;//渠道类型
        $data['remark']      = '';//渠道类型

        $signText            = $data['sn'] . $data['merchant'] . $data['totalAmount'] . $data['subject'] . $data['callBack'] . $data['channel'] . $payConf['md5_private_key'];
        $signValue           = md5($signText);
        $data['sign']        = $signValue;
        $aes_content         = $signValue . $data['busiType'];//要加密的内容
        $keyStr16            = self::generate_randsn(16);
        $data['encryptData'] = self::sign_aes($aes_content, $keyStr16);;//加密后的请求报文
        $data['encryptKey'] = self::sign_rsa($keyStr16, $payconf['public_key']);

        unset($reqData);
        return $data;
    }

    private static function generate_randsn($length = 28)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $sn    = '';
        for ($i = 0; $i < $length; $i++) {
            $sn .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $sn;
    }

    private static function sign_aes($aes_content, $keyStr16)
    {
        $cipher  = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv_size = mcrypt_enc_get_iv_size($cipher);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        if (mcrypt_generic_init($cipher, $keyStr16, $iv) != -1) {
            $cipherText = mcrypt_generic($cipher, self::pad2Length($aes_content, 16));
            mcrypt_generic_deinit($cipher);
            mcrypt_module_close($cipher);

            return base64_encode($cipherText);
        }

    }

    private static function sign_rsa($keyStr16, $pubilc_keys)
    {
        openssl_public_encrypt($keyStr16, $signMsg, $pubilc_keys, OPENSSL_PKCS1_PADDING); //×¢²áÉú³É¼ÓÃÜÐÅÏ¢
        return base64_encode($signMsg); //base64×ªÂë¼ÓÃÜÐÅÏ¢
    }

    private static function pad2Length($text, $padlen)
    {
        $len  = strlen($text) % $padlen;
        $res  = $text;
        $span = $padlen - $len;
        for ($i = 0; $i < $span; $i++) {
            $res .= chr($span);
        }
        return $res;
    }

}