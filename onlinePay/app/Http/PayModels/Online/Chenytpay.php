<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;
use App\Http\Models\CallbackMsg;

class Chenytpay extends ApiModel
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
        if ($payConf['pay_way'] != 9) {
            self::$reqType        = 'curl';
            self::$payWay         = $payConf['pay_way'];
            self::$resType        = 'other';
            self::$httpBuildQuery = true;
        }

        $payInfo = explode('@', $payConf['business_num']);

        if(!isset($payInfo[1])){
            echo '绑定格式错误!请参考:商户号@终端号';exit();
        }

        if(!openssl_get_publickey($payConf['public_key'])){
            echo '公钥绑定错误!';exit();
        }

        if(!openssl_get_privatekey($payConf['rsa_private_key'])){
            echo ' 私钥绑定错误!';exit();
        }

        $params = array(
            'p1_mchtid'      => $payInfo[0],
            'p2_paytype'     => $bank,
            'p3_paymoney'    => sprintf('%.2f', $amount),
            'p4_orderno'     => $order,
            'p5_callbackurl' => $ServerUrl,
            'p6_notifyurl'   => $returnUrl,
            'p7_version'     => 'v2.9',
            'p8_signtype'    => 2,
            'p9_attach'      => 'test',
            'p10_appname'    => 'test',
            'p11_isshow'     => 0,
            'p12_orderip'    => self::getIp(),
            'p13_memberid'   => self::$userName,
        );

        $signStr        = self::get_sign($params);
        $params['sign'] = md5($signStr . $payInfo[1]);

        //转为json字符串
        $dataJson = json_encode($params);
        //RSA公钥加密
        $reqdata = urlencode(self::publicEncrypt($payConf['public_key'], $dataJson));

        $data['mchtid']      = $payInfo[0];
        $data['reqdata']     = $reqdata;
        $data['p4_orderno']  = $params['p4_orderno'];
        $data['p3_paymoney'] = $params['p3_paymoney'];
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['rspCode'] == "1") {
            $data['qrCode'] = $data['data']['r6_qrcode'];
        }
        return $data;
    }

    //回调特殊处理  转换req值
    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['reqdata'])) {
            $reqdata = $arr['reqdata'];
            if (strpos($reqdata, "%")) {
                $reqdata = urldecode($reqdata);
            }
        } else {
            $reqdata = array();
        }
        $bankOrder = PayOrder::getOrderData($arr['ordernumber']);//根据订单号 获取入款注单数据
        if (isset($bankOrder->merchant_id)) {
            $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        }else{
            CallbackMsg::addDebugLogs($arr);
            echo trans("success.{$mod}");exit();
        }
        $dataJson = self::privateDecrypt($reqdata, $payConf['rsa_private_key']);
        $data     = json_decode($dataJson, true);
        unset($data["sysnumber"]);
        unset($data["attach"]);
        self::$dataJson = $data;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $res    = self::$dataJson;
        $payInfo = explode('@', $payConf['business_num']);
        //去除不需要参入验签的字段
        $re = self::payVerify($res, $payInfo[1]);
        if ($re && $res['orderstatus'] == '1') {
            return true;
        }
        return false;
    }

    public static function get_sign($arr)
    {
        $signmd5 = "";
        foreach ($arr as $x => $x_value) {
            if ($signmd5 == "") {
                $signmd5 = $signmd5 . $x . '=' . $x_value;
            } else {
                $signmd5 = $signmd5 . '&' . $x . '=' . $x_value;
            }
        }
        return $signmd5;
    }

    public static function publicEncrypt($publicKey, $data)
    {

        $key = openssl_get_publickey($publicKey);

        $original_arr = str_split($data, 117);
        foreach ($original_arr as $o) {
            $sub_enc = null;
            openssl_public_encrypt($o, $sub_enc, $key);
            $original_enc_arr[] = $sub_enc;
        }

        openssl_free_key($key);
        $original_enc_str = base64_encode(implode('', $original_enc_arr));
        return $original_enc_str;
    }

    public static function privateDecrypt($data, $privateKey)
    {
        //读取秘钥
        $pr_key = openssl_pkey_get_private($privateKey);
        if ($pr_key == false) {
            echo "打开密钥出错";
            die;
        }
        $data   = base64_decode($data);
        $crypto = '';
        //分段解密
        foreach (str_split($data, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $pr_key);
            $crypto .= $decryptData;
        }
        return $crypto;
    }

    public static function payVerify($result, $md5)
    {

        $signStr    = $result['sign'];
        $sign_array = array();
        foreach ($result as $k => $v) {
            if ($k !== 'sign') {
                $sign_array[$k] = $v;
            }
        }
        $sign = md5(self::get_sign($sign_array) . $md5);
        if ($signStr != $sign) {
            return false;
        }
        return true;
    }
}