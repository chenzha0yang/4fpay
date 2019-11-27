<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tangpay extends ApiModel
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
        $data = [];
        $data['merid'] = $payConf['business_num'];//商户编号
        $data['Merorderno'] = $order;//商户订单号
        $data['Toamount'] = $amount;//金额
        $data['Return_url'] = $returnUrl;//返回地址
        $data['Paymentype'] = $bank;      //支付类型
        if ( $payConf['pay_way'] == '1' ) {
            $data['Paymentype'] = '5';
        }
        $data['remark'] = $order;//备注
        $data['Notifyurl'] = $ServerUrl;//回调地址
        $sign = self::encrypt(json_encode($data,JSON_UNESCAPED_SLASHES),$payConf['md5_private_key']);
        if($payConf['pay_way'] == '6'){
            $data['Sign'] = str_replace("+","%2b",$sign);//签名
        }else{
            $data['Sign'] = $sign;//签名
        }
        $dataUrl = "merid=".$data['merid']."&Sign=".$data['Sign'];
        if ($payConf['pay_way'] == '1' || $payConf['pay_way'] == '6') {
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            unset($reqData);
            return $dataUrl;
        }
        
        unset($reqData);
        return $data;
    }

    /*
    * 加密输入的字符串
    */
    public static  function encrypt($input,$key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = self::pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    /*
    * 解密字符串
    */
    public static  function decrypt($sStr,$key) {
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $key,
            base64_decode($sStr),
            MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }

    /*
    * 填充模式
    */
    public static  function pkcs5_pad ($text, $blocksize){
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * @param $type
     * @param $sign
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $Sign = str_replace("%2B","+",$data['Sign']);
        $data = self::decrypt($Sign,$payConf['md5_private_key']);
        $mySign = self::encrypt($data,$payConf['md5_private_key']);
        $json_de = json_decode($data,true);
        if($mySign == $Sign && $json_de['payStatus'] == 'TRADE_SUCCESS'){
            return true;
        } else {
            return false;
        }
    }

}