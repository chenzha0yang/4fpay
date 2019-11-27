<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Manbapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

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

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        self::$unit = '2';

        $data = [];
        if ( $payConf['pay_way'] == 2 ) {
            $type = 'WEIXIN';
        } elseif ( $payConf['pay_way'] == 3 ) {
            $type = 'ALIPAY';
        } elseif ( $payConf['pay_way'] == 4 ) {
            $type = 'QQPAY';
        } elseif ( $payConf['pay_way'] == 6 || $payConf['pay_way'] == 1 ) {
            $type = 'UNIONPAY';
        } else {
            $type = '';
        }
        if ( $payConf['pay_way'] == 1 ) {
            $bank = 'GATEWAY_UNIONPAY_ONE';
            $data['userId '] = '';
            $data['appId'] = '';
        }
        $data['merAccount'] = $payConf['public_key'];   //商户标识，由系统随机生成
        $data['merNo'] = $payConf['business_num'];      //商户号
        $data['orderId'] = $order;                      //订单
        $data['time'] = time();                         //时间戳
        $data['amount'] = $amount * '100';
        $data['productType'] = '01';                    //固定值 01
        $data['product'] = 'apple';                     //商品
        $data['productDesc'] = 'desc';                  //商品描述
        $data['userType'] = '0';                        //用户类型，固定值 0
        $data['payWay'] = $type;                        //支付方式
        $data['payType'] = $bank;                       //支付类型
        $data['userIp'] = '192.168.0.1';
        $data['returnUrl'] = $ServerUrl;
        $data['notifyUrl'] = $ServerUrl;
        ksort($data);
        $signStr = '';
        foreach ( $data as $key => $value ) {
            $signStr .= $value;
        }
        $data['sign'] = sha1($signStr . $payConf['md5_private_key']);
        ksort($data);
        $parAms = self::encrypt(json_encode($data), $payConf['md5_private_key']);
        $postData = array(
            'merAccount' => $payConf['public_key'],
            'data' => $parAms,
            'orderId' => $order,
            'amount' => $amount * '100'
        );
        unset($reqData);
        return $postData;
    }

    //参数加密
    public static function encrypt($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $pad = $size - ( strlen($input) % $size );
        $input = $input . str_repeat(chr($pad), $pad);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    //提交二维码处理
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        $result['qrCode'] = $result['data']['qrCode'];
        return $result;
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $dKey = $payConf['md5_private_key'];
        $decryPted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $dKey, base64_decode($data['data']), MCRYPT_MODE_ECB);
        $padding = ord($decryPted[strlen($decryPted)-1]);
        $decryPted = substr($decryPted, 0, -$padding);
        $jsonData = json_decode($decryPted, true);
        ksort($jsonData);
        $str = '';
        foreach ( $jsonData as $key => $value ) {
            if ( $value <> 'sign' ) {
               $str .= $value;
            }
        }
        $signStr = sha1($str . $dKey);
        if ( $signStr == $jsonData['sign'] && $jsonData['orderStatus'] == 'SUCCESS' ) {
            return true;
        } else {
            return false;
        }
    }
}