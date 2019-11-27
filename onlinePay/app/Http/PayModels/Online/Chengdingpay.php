<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Chengdingpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $is_app = '';

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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';

        $data['ordercode'] = $order;
        $data['amount']    = $amount;
        if ($payConf['pay_way'] == '1') {
            $data['bankname'] = $bank;
        } else {
            $data['goodsId'] = $bank;
        }
        $data['statedate']   = date('Ymd', time());
        $data['merNo']       = $payConf['business_num'];
        $data['callbackurl'] = $ServerUrl;
        $data['notifyurl']   = $returnUrl;
        $data['returnurl']   = $returnUrl;
        $signStr             = json_encode($data);
        $str                 = openssl_encrypt($signStr, 'des-cbc', $payConf['md5_private_key'], true, $payConf['md5_private_key']);
        $str                 = strtoupper(bin2hex($str));
        Curl::$request       = $str;
        //请求网关
        Curl::$url = $reqData['formUrl'] . $payConf['business_num'];
        //请求
        $jsonData = Curl::Request();
        $res      = self::decrypt($jsonData, $payConf['md5_private_key']);
        $result   = json_decode($res, true);
        if ($result['result'] == 200) {
            if ($payConf['is_app'] == 1) {
                echo "<script type='text/javascript'>window.location.replace('{$result['CodeUrl']}');</script>";
                exit();
            } else {
                echo "<script type='text/javascript'>window.location.replace('{$result['imageValue']}');</script>";
                exit();
            }
        } else {
            echo $res;
            exit;
        }
        unset($reqData);
        return $data;
    }

    public static function hex2bin($hexData)
    {
        $binData = "";
        for ($i = 0; $i < strlen($hexData); $i += 2) {
            $binData .= chr(hexdec(substr($hexData, $i, 2)));
        }
        return $binData;
    }

    public static function decrypt($str, $key)
    {
        $strBin = self::hex2bin(strtolower($str));
        $data   = openssl_decrypt($strBin, 'des-cbc', $key, true, $key);
        return $data;
    }

    public static function getVerifyResult($request)
    {
        $json          = $request->getContent();
        $res           = json_decode($json, true);
        $res['amount'] = $res['amount'] / 100;
        return $res;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $json     = file_get_contents('php://input');
        $data     = json_decode($json, true);
        $sign     = $data['sign'];
        $signStr  = $data['ordercode'] . $data['amount'] . $data['goodsId'] . $payConf['md5_private_key'];
        $signTrue = md5($signStr);
        if ($sign == $signTrue && $data['result'] == '200') {
            return true;
        }
        return false;
    }
}