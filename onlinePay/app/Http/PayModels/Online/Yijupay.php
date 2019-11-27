<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;

class Yijupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $userName = '';

    public static $dataJson = '';

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
        $order          = $reqData['order'];
        $amount         = $reqData['amount'];
        $bank           = $reqData['bank'];
        $ServerUrl      = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl      = $reqData['returnUrl']; // 同步通知地址
        self::$userName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //TODO: do something
        self::$reqType        = 'curl';
        self::$payWay         = $payConf['pay_way'];
        self::$resType        = 'other';
        self::$httpBuildQuery = true;
        self::$isAPP          = true;
        $data['userOrderId']  = $order;
        $data['userUid']      = self::$userName;
        $data['productName']  = 'test';
        $data['money']        = $amount * 100;
        $data['type']         = $bank;
        $data['time']         = self::getMillisecond();
        $data['notifyUrl']    = $ServerUrl;
        $data['returnUrl']    = $returnUrl;

        $jsonData = json_encode($data);
        $pub_key  = openssl_pkey_get_public($payConf['public_key']);
        $ret      = '';
        $strArray = str_split($jsonData, 117);
        foreach ($strArray as $cip) {
            if (openssl_public_encrypt($cip, $result, $pub_key, OPENSSL_PKCS1_PADDING)) {
                $ret .= $result;
            }
        }
        $newOrderEncryptData = base64_encode($ret);

        $newOrderReqData['userName']    = $payConf['business_num'];
        $newOrderReqData['data']        = $newOrderEncryptData;
        $newOrderReqData['userOrderId'] = $data['userOrderId'];
        $newOrderReqData['money']       = $data['money'];
        unset($reqData);
        return $newOrderReqData;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == 'success') {
            $data['qrCode'] = $data['order']['payUrl'];
        }
        return $data;
    }

    //回调特殊处理  转换req值

    /**
     * @param $request
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $arr = $request->all();
        if (isset($arr['orderInfo'])) {
            $orderInfo           = $arr['orderInfo'];
            $orderJson           = base64_decode($orderInfo);
            $orderData           = json_decode($orderJson, true);
            $data['orderInfo']   = $arr['orderInfo'];
            $data['sign']        = $arr['sign'];
            $data['money']       = $orderData['money'] / 100;
            $data['userOrderId'] = $orderData['userOrderId'];
        } else {
            $data['userOrderId'] = '';
            $data['money']       = '';
        }

        return $data;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $orderInfo = $data['orderInfo'];
        $sign      = $data['sign'];

        $orderJson = base64_decode($orderInfo);
        $orderData = json_decode($orderJson, true);

        ksort($orderData);
        $signStr = "";
        $flag    = 0;
        foreach ($orderData as $key => $val) {
            if ($val === "") {
                continue;
            }
            if ($flag === 1) {
                $signStr = $signStr . "&";
            }
            $signStr = $signStr . $key . "=" . $val;
            $flag    = 1;
        }

        $pub_id = openssl_get_publickey($payConf['public_key']);
        $res    = openssl_verify($signStr, base64_decode($sign), $pub_id, OPENSSL_ALGO_MD5);


        if ($res && $orderData['status'] == '5') {
            return true;
        }
        return false;
    }

    /**
     * @return float
     */
    public static function getMillisecond()
    {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }
}