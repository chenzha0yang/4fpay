<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Suibianmallpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $piKey = '';
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
        self::$piKey = $payConf['rsa_private_key'];
        self::$isAPP = true;
        self::$unit = 2;
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$httpBuildQuery = true;
        //TODO: do something
        $payInfo = explode('@', $payConf['business_num']);

        if(!isset($payInfo[1])){
            echo '商户号请填入格式为：商户号@机构号 的商户信息';exit;
        }

        $postData['orgNo'] = $payInfo[1];//机构号
        $postData['merNo'] = $payInfo[0];//商户号
        $postData['action'] = $bank;

        $data = [
            'linkId'       => $order,
            'orderType'       => '10',
            'goodsName'     =>'test',
            'amount'        => $amount*100,
            'notifyUrl'     => $ServerUrl,
            'frontUrl'   => $returnUrl,
        ];

        //随机密钥明文
        $encryptkeyMw = self::ASEgenerate();

        //随机密钥密文
        $encryptkeyMi = self::encrypt($encryptkeyMw, self::$piKey);
        $postData['encryptkey'] = $encryptkeyMi;

        $jsonData = json_encode($data, 256);
        //密文Data请求数据
        $dataMi = self::aesEncrypt($jsonData, $encryptkeyMw);
        $postData['data'] = $dataMi;

        $signStr = $postData['orgNo'] . "" . $postData['merNo'] . "" . $postData['action'] . "" . $dataMi . $payConf['md5_private_key'];
        $postData['sign'] = md5($signStr);
        $postData['linkId'] = $data['linkId'];
        $postData['amount'] = $data['amount'];
        unset($reqData);
        return $postData;
    }

    /**
     * 返回结果 - 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $backData = json_decode($response, true);
        if ($backData['respCode'] == '200') {
            $encryptkey = $backData['encryptkey'];
            $decryptkey = self::decrypt($encryptkey, self::$piKey);
            $data = $backData['data'];
            $decryptData = self::aesDecrypt($data, $decryptkey);

            $datas = json_decode($decryptData,true);
            if($datas['code'] == '000000'){
                $res['payUrl'] = $datas['payUrl'];
            }
            $res['code'] = $datas['code'];
            $res['msg'] = $datas['msg'];
        } else {
            $res['code'] = $backData['respCode'];
            $res['msg']  = $backData['respMsgs'];
        }

        return $res;
    }

     public static function getVerifyResult($request, $mod)
    {
        $data               = $request->all();
        $data['orderAmount'] = $data['orderAmount'] / 100;
        $data['linkId']     = $data['linkId'];
        return $data;
    }
    
    /**
     * 回掉特殊处理
     * @param $model
     * @param $data - 返回的数据 - array
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr         =  $data['orderNo'].$data['orderStatus'].$payConf['md5_private_key'];
        $signTrue            =  md5($signStr);
        if(strtoupper($sign) == strtoupper($signTrue) && $data['orderStatus'] == '20'){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 生成AESKey
     * @param $size
     * @return string
     */
    public static function ASEgenerate($size = 16)
    {
        $str = 'ABCDEF123456789';
        $arr = array();
        for ($i = 0; $i < $size; $i++) {
            $arr[] = $str[mt_rand(0, 14)];
        }

        return implode('', $arr);
    }

    /**
     * 私钥加密
     * @author hebidu <email:346551990@qq.com>
     * @param $data
     * @param $key
     * @return string  返回的是base64加密过的
     * @throws CryptException
     */
    public static function encrypt($data, $private_key)
    {
        $result = openssl_private_encrypt($data, $sign_info, $private_key, OPENSSL_PKCS1_PADDING);//私钥加密

        if (!$result) {
            echo '签名出错';exit();
        }

        return base64_encode($sign_info);
    }

    /**
     * key 必须是16位
     * @param $content
     * @param $key
     * @return string
     */
    public static function aesEncrypt($content, $key)
    {
        if (is_null($key)) {
            echo '未获取到密钥';exit();
        }
        $strEncode = openssl_encrypt($content, 'aes-128-ecb', $key, OPENSSL_RAW_DATA, '');
        $strEncode = bin2hex($strEncode);
        return $strEncode;
    }

    public static function aesDecrypt($contents, $key)
    {
        if (is_null($key)) {
            echo '未获取到密钥';exit();
        }
        $sKey = substr($key, 0, 16);
        $contents = hex2bin(strtolower($contents));
        return openssl_decrypt($contents, 'aes-128-ecb', $sKey, OPENSSL_RAW_DATA, '');
    }

    /**
     * 私钥解密
     * @author hebidu <email:346551990@qq.com>
     * @param $data
     * @param $private_key
     * @return string
     * @throws CryptException
     */
    public static function decrypt($data, $private_key)
    {
        $pi_key = openssl_pkey_get_private($private_key);

        if ($pi_key === false) {
            echo '私钥获取失败';exit();
        }

        $decrypt = "";
        $ret = openssl_private_decrypt(base64_decode($data), $decrypt, $private_key);

        if ($ret == false) {
            echo '签名出错';exit();
        }

        return $decrypt;
    }
}