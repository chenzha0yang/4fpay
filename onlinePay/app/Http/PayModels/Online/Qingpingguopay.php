<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Qingpingguopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $resData = '';

    private static $UserName = '';

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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$method   = 'header';
        self::$resType  = 'other';
        self::$postType = true;

        if (openssl_get_privatekey($payConf['rsa_private_key']) != true) {
            echo "rsa私钥格式不对，请修改后重新编辑";exit;
        }
        if (openssl_pkey_get_public($payConf['public_key']) != true) {
            echo "rsa公钥格式不对，请修改后重新编辑";exit;
        }
        self::$resData = $payConf['public_key'];
        $data['orderAmountRmb'] = sprintf('%.2f', $amount);
        $data['subject']        = 'qpg';
        $data['outTradeNo']     = $order;
        $data['signType']       = 'RSA';
        $data['body']           = 'Iphone6';
        $data['merchantName']   = $payConf['business_num'];
        $data['vipName']        = self::$UserName;
        $data['notifyUrl']      = $ServerUrl;
        //排序post数据
        $signStr = self::getSignStr($data, true, true);
        openssl_sign($signStr, $signature, openssl_get_privatekey($payConf['rsa_private_key']));
        //加签名
        $data['sign']        = base64_encode($signature);
        $post['order']       = $order;
        $post['amount']      = $amount;
        $post['data']        = json_encode($data);
        $post['httpHeaders'] = [
            "Content-Type: application/json; charset=utf-8"
        ];
        unset($reqData);
        return $post;
    }

    public static function getRequestByType($post)
    {
        return $post['data'];
    }


    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
            if ($result['code'] == '200') {
                $sign = base64_decode($result['data']['sign']);
                unset($result['data']['sign']);
                $result['data']['orderAmountRmb'] = sprintf('%.2f', $result['data']['orderAmountRmb']);
                $signStr = self::getSignStr($result['data'], true, true);
                $check_sig = openssl_verify($signStr, $sign, openssl_pkey_get_public(self::$resData), OPENSSL_ALGO_SHA1);
                if ($check_sig) {
                    $result['qrCode'] = $result['data']['returnUrl'];
                } else {
                    $result['msg'] = '公钥验证不通过，请填写正确的公钥';
                }
            } else {
                $msg = '';
                if (isset($result['msgDebug'])) {
                    $msg = $result['msgDebug'];
                }
                $result['msg'] = $result['message'] . $msg;
            }
        return $result;
    }

    //回调金额化分为元
    public static function getVerifyResult($request, $mod)
    {
        $arr                    = $request->getContent();
        $res                    = json_decode($arr, true);
        $data['orderAmountRmb'] = $res['orderAmountRmb'];
        $data['outTradeNo']     = $res['outTradeNo'];
        return $data;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $json, $payConf)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $sign = base64_decode($data['sign']);
        unset($data['sign']);
        ksort($data);
        $data['orderAmountRmb']   = sprintf("%.2f", $data['orderAmountRmb']);
        $data['receiveAmountGac'] = sprintf("%.2f", $data['receiveAmountGac']);
        $data['userpayAmountRmb'] = sprintf("%.2f", $data['userpayAmountRmb']);
        $signStr                  = self::getSignStr($data, true, true);
        $check_sign               = openssl_verify($signStr, $sign, openssl_pkey_get_public($payConf['public_key']), OPENSSL_ALGO_SHA1);
        if ($check_sign == 1 && $data['status'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}