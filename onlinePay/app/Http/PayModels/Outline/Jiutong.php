<?php

namespace App\Http\PayModels\Outline;

use App\ApiModel;
use App\Http\Models\OutMerchant;
use App\Http\Models\Client;
use App\Http\Models\OutOrder;

class Jiutong extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'curl'; //提交类型 必加属性 form or curl or fileGet

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    /**
     * @param array $reqData 接口传递的参数
     * @param OutMerchant $payConf object OutMerchant类型的对象
     * @return string
     */
    public static function getAllInfo($reqData, OutMerchant $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order']; // 订单号
        $amount    = $reqData['amount']; // 金额
        $bankCode  = $reqData['bankCode']; // 出款银行编码
        $bankCard  = $reqData['bankCard']; // 出款银行卡号
        $bankName  = $reqData['bankName']; // 出款银行信息
        $cardName  = $reqData['cardName']; // 出款真实姓名
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址

        //TODO: do something

        $data                    = array();
        $data['version']         = "V3.1.0.0";//版本号，固定值：V3.1.0.0  8   是
        $data['merNo']           = $payConf['business_num'];//    商户号 16  是
        $data['orderNum']        = $order;//订单号  20  是
        $money                   = $amount * 100;
        $data['amount']          = "{$money}";//金额，（单位：分）   14  是
        $data['bankCode']        = $bankCode;//银行代码，参考附录3.4 20  是
        $data['bankAccountName'] = $cardName;//开户名    128 是
        $data['bankAccountNo']   = $bankCard;//银行卡号 128 是
        $data['callBackUrl']     = $ServerUrl;//结果通知地址    128 是
        $data['charset']         = "UTF-8";//客户端系统编码格式    10  是
        $key                     = Client::getClientKey($payConf['client_id']);
        $md5Key                  = self::decrypt($payConf['md5_private_key'], $key->secret);
        $remitPublicKey          = self::decrypt($payConf['public_key'], $key->secret);
        ksort($data);
        $sign         = strtoupper(md5(json_encode($data, 320) . $md5Key));
        $data['sign'] = $sign;
        //转成json字符串
        $json = json_encode($data, 320);
        //加密
        $dataStr = urlencode(static::encodePay($json, $remitPublicKey));
        //请求原文
        $param = "data={$dataStr}&merchNo={$data['merNo']}&version={$data['version']}";
        unset($reqData);
        return $param;
    }

    /**
     * @param $data
     * @param $payPublicKey
     * @return string
     */
    private static function encodePay($data, $payPublicKey)
    {
        $encryptData = '';
        $crypt       = '';
        foreach (str_split($data, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $payPublicKey);
            $crypt = $crypt . $encryptData;
        }
        $crypt = base64_encode($crypt);
        return $crypt;
    }

    /**
     * @param $response
     * @param OutMerchant $payConf
     * @param $orderData
     * @return string
     */
    public static function getResponse($response, OutMerchant $payConf, $orderData)
    {
        $array  = json_decode($response, true);
        $result = array();
        if ($array['stateCode'] == '00') {
            $signString = $array['sign'];
            ksort($array);
            $signArray = array();
            foreach ($array as $k => $v) {
                if ($k !== 'sign') {
                    $signArray[$k] = $v;
                }
            }
            // 生成签名 并将字母转为大写
            $md5 = strtoupper(md5(json_encode($signArray, 320) . $payConf['md5_private_key']));
            if ($md5 == $signString) {
                $result['returnCode'] = 'SUCCESS'; // 固定返回 成功的状态
                $result['returnMsg']  = '出款成功'; // 固定返回 成功的信息
            } else {
                $result['returnCode'] = 'SIGN_ERROR';  // 固定返回 验签失败的状态
                $result['returnMsg']  = '签名验证失败'; // 固定返回 验签失败的信息
            }
        } else {
            $result['returnCode'] = $array['stateCode'];
            $result['returnMsg']  = $array['msg'];
        }
        if ($result['returnCode'] != 'SUCCESS') {
            OutOrder::updateErrorOrder($orderData['order'], json_encode($result));
        }
        return json_encode($result);
    }


    public static function SignOther($model, $data, $payConf)
    {
        $key        = Client::getClientKey($payConf['client_id']);
        $md5Key     = self::decrypt($payConf['md5_private_key'], $key->secret);
        $privateKey = self::decrypt($payConf['private_key'], $key->secret);
        $data       = static::decode($data['data'], $privateKey);
        return static::callbackToArray($data, $md5Key);
    }


    /**
     * @param $data
     * @param $prKey
     * @return string
     */
    public static function decode($data, $prKey)
    {
        $data = base64_decode($data);
        $Str  = '';
        //分段解密
        foreach (str_split($data, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $prKey);
            $Str .= $decryptData;
        }
        return $Str;
    }


    /**
     * @param $json
     * @param $key
     * @return bool
     */
    public static function callbackToArray($json, $key)
    {
        $array = json_decode($json, true);
        if ($array['stateCode'] == '00') {
            $signStr = $array['sign'];
            ksort($array);
            $signArray = array();
            foreach ($array as $k => $v) {
                if ($k !== 'sign') {
                    $signArray[$k] = $v;
                }
            }
            // 生成签名 并将字母转为大写
            $md5 = strtoupper(md5(json_encode($signArray, 320) . $key));
            if ($md5 == $signStr) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}