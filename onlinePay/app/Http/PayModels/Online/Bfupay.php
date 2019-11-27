<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bfupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $callbackData = '';

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
        self::$method = 'get';
        self::$httpBuildQuery = true;

        $data = [];
        $data['merid'] = $payConf['business_num'];                                  //商户编号
        $data['Toamount'] = $amount;                                                //金额
        $data['Merorderno'] = $order;                                               //商户订单号
        $data['Return_url'] = $returnUrl;                                           //返回地址
        $data['Paymentype'] = $bank;                                                //支付类型
        $sign = self::encrypt(json_encode($data, TRUE), $payConf['md5_private_key']);
        $datas['merid'] = $payConf['business_num'];
        $datas['Sign'] = str_replace("+","%2B", $sign);                             //签名
        unset($reqData);
        return $datas;
    }

    //加密
    public static function encrypt($jsonStr, $key)
    {
        $key = base64_decode($key);
        $jsonStr = trim($jsonStr);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $pad = $block - (strlen($jsonStr) % $block);
        if ($pad <= $block) {
            $jsonStr .= str_repeat(chr($pad), $pad);
        }
        $encrypt_str = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $jsonStr, MCRYPT_MODE_ECB, $key);
        return base64_encode($encrypt_str);
    }

    //回调
    public static function SignOther($type, $data, $payConf)
    {
        $Sign = str_replace("%2B", "+", $data['Sign']);
        $datas = self::decrypt($data['Sign'], $payConf['md5_private_key']);
        $mySign = self::encrypt($datas, $payConf['md5_private_key']);
        $datas = json_decode($datas, true);
        $total_fee = sprintf("%.2f", $datas['Totalamount']);
        if ($mySign == $Sign && $datas['payStatus'] == 'TRADE_SUCCESS') {
            return true;
        } else {
            return false;
        }
    }

    //解密
    public static function decrypt($jsonStr, $key)
    {
        $jsonStr = base64_decode($jsonStr);
        $key = base64_decode($key);
        $encryptStr = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $jsonStr, MCRYPT_MODE_ECB, $key);
        $encryptStr = trim($encryptStr);
        $char = substr($encryptStr, -1);
        $num = ord($char);
        if ($num > 32) return $encryptStr;
        $encryptStr = substr($encryptStr, 0, -$num);
        return $encryptStr;
    }

    public static function getVerifyResult($request)
    {
        $data = $requset->all();
        $datas = self::decrypt($data['Sign'], $payConf['md5_private_key']);
        self::$callbackData = json_decode($datas,true);
        $res['amount'] = self::$callbackData['Toamount'];  //金额
        $res['order']  = self::$callbackData['Merorderno']; //订单号
        return $res;
    }
}