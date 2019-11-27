<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\File;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;
use Illuminate\Http\Request;

class Yidzfpay extends ApiModel
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
     * @param array $reqData 接口传递的参数
     * @param array $payConf
     * @return array|string
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

        // TODO
        $data               = array();
        $data['amount']     = $amount;
        $data['version']    = '1.0.1';
        $data['notifyUrl']  = $ServerUrl;
        $data['tranTp']     = 0;
        $data['orgOrderNo'] = $order;
        $data['subject']    = 'chongzhi';
        $data['extra_para'] = $order;
        $data['returnUrl']  = $returnUrl;
        if ($payConf['pay_way'] == '1') {
            $data['merchantCode'] = $payConf['business_num']; //商户编号
            $data['num']          = '1';
            $data['desc']         = '1';
            $data['bankWay']      = 'GATEWAY'; //付款方式
            $data['source']       = 'GATEWAY';
        } else {
            $data['source'] = $bank; //付款方式
        }
        $post = [];
        if ($payConf['pay_way'] == '1') {
            $post = self::getSign($data, $payConf);
        } else {
            $post['data']       = self::getSign($data, $payConf);
            $post['orgOrderNo'] = $order;
            $post['amount']     = $amount;
            self::$reqType      = 'curl';
            self::$resType      = 'other';
            self::$postType     = true;
            self::$payWay       = $payConf['pay_way'];
            $array              = ['WXH5', 'ZFBH5', 'YLH5', 'QQH5', 'JDH5'];
            if (in_array($bank, $array)) {
                self::$isAPP = true;
            }
        }
        unset($reqData);
        return $post;
    }

    public static function getRequestByType($data)
    {
        return $data['data'];
    }


    public static function getQrCode($resp)
    {
        $response = json_decode($resp, true);
        if ($response['responseCode'] == '200' && $response['responseMessage'] == 'SUCCESS') {
            if($response['responseObj']['respCode'] == '0000'){
                $qrCode           = $response['responseObj']['qrCode'];
                $return['payUrl'] = $qrCode;
            } else {
                $return['respMsg']  = $response['responseObj']['respMsg'];
                $return['respCode'] = $response['responseObj']['respCode'];
            }
        } else {
            $return['respMsg']  = $response['responseMessage'];
            $return['respCode'] = $response['responseCode'];
        }
        return $return;
    }


    public static function getVerifyResult(Request $request, $mod)
    {
        $arr       = json_decode(stripslashes($request['reqJson']), true);
        $orderNO   = $arr['extra_para'];
        $transData = $arr['transData'];
        $bankOrder = PayOrder::getOrderData($orderNO);//根据订单号 获取入款注单数据
        if (empty($bankOrder)) {
            //查询不到订单号时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
            File::logResult($request->all());
            return trans("success.{$mod}");
        }
        $payMerchant = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        $return      = [];
        if ($data = self::VerifySign($transData, $payMerchant)) {
            if ($data['isClearOrCancel'] == "0") {
                $return['totalAmount'] = $data['totalAmount'];
                $return['order']  = $orderNO;
            }
        } else {
            $return['totalAmount'] = '';
            $return['extra_para']  = '';
        }
        return $return;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        return true;
    }

    /**
     * @param $arr
     * @param $payConf
     * @return array|string
     */
    public static function getSign($arr, $payConf)
    {
        $str                      = self::sortData($arr);
        $baseStr                  = base64_encode($str);
        $aes                      = self::getEncrypt($baseStr, $payConf['rsa_private_key']);
        $aes                      = strtoupper($aes);
        $sign                     = strtoupper(md5($aes . $payConf['md5_private_key']));
        $arr['sign']              = $sign;
        $str2                     = self::sortData($arr);
        $baseStr2                 = base64_encode($str2);
        $transData                = self::getEncrypt($baseStr2, $payConf['rsa_private_key']);
        $jsonData                 = array();
        $jsonData['merchantCode'] = $payConf['business_num']; //商户编号
        $jsonData['transData']    = $transData;
        if ($payConf['pay_way'] == '1') {
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
    public static function sortData($arr)
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
     * @param String $string 加密的字符串
     * @param String $key 解密的key
     * @return String
     */
    public static function getEncrypt($string, $key)
    {
        $data = openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = bin2hex($data);
        return $data;
    }

    /**
     * 解析验证签名
     *
     * @param $str
     * @param $pay
     * @return array|bool
     */
    public static function VerifySign($str, $pay)
    {
        //解密
        $secDec = self::getDecrypt($str, $pay['rsa_private_key']);
        $secDec = base64_decode($secDec);
        //分割
        $pra       = explode("&", $secDec);
        $resultPra = array(); //异步回调的参数
        foreach ($pra as $item) {
            $tempPra                = explode("=", $item);
            $resultPra[$tempPra[0]] = $tempPra[1];
        }
        $sign = $resultPra["sign"];
        //移除sign
        unset($resultPra["sign"]);
        $result_str = self::sortData($resultPra);
        $baseStr    = base64_encode($result_str);
        $ase        = self::getEncrypt($baseStr, $pay['rsa_private_key']);
        $ase        = strtoupper($ase);
        $sign2      = strtoupper(md5($ase . $pay['md5_private_key']));
        if ($sign == $sign2) {
            return $resultPra;
        } else {
            return false;
        }
    }

    /**
     * 解密
     * @param String $string 解密的字符串
     * @param String $key 解密的key
     * @return String
     */
    public static function getDecrypt($string, $key)
    {
        $decrypted = openssl_decrypt(self::hex2bin($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $decrypted;
    }

    public static function hex2bin($data)
    {
        $len = strlen($data);
        $str = '';
        for ($i = 0; $i < $len; $i += 2) {
            $str .= pack("C", hexdec(substr($data, $i, 2)));
        }
        return $str;
    }

}