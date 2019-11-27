<?php

namespace App\Http\PayModels\Outline;

use App\ApiModel;

class Shunweidfpay extends ApiModel
{
    public static $method = 'HEADER'; //提交方式 必加属性 post or get

    public static $outReqType = 'curl'; //提交类型 必加属性curl or fileGet

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组 getRequestByType

    public static $OutQryUrl = 'https://api.shunwpay.com/gateway/query/remit/realtime-remittance';//代付查询地址

    /**
     * 代付请求接口
     * @param $reqData
     * @param $payConfig
     * @return array
     */
    public static function outGetAllInfo($reqData, $payConfig)
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

        //基本参数
        $pt                       = explode('&', $payConfig['business_num']);
        $pay_id                   = $pt[0];//商户号
        $app_id                   = $pt[1];//唯一标识
        $arr["client_num"]        = $pay_id;//商户号
        $arr["order_num"]         = (string)($order);//订单号
        $arr["amount"]            = (string)($amount * 100);//订单金额
        $arr["bank_account_name"] = $cardName;//开户名
        $arr["bank_account_no"]   = $bankCard;
        $arr["bank_code"]         = $bankCode;
        $randStr                  = static::generateRandNum(count($arr) + 1);//随机字符串
        $arr["random_str"]        = $randStr;
        $params                   = static::getSignArr($arr, $randStr);//待签名数组
        $jsonStr                  = json_encode($params, JSON_UNESCAPED_UNICODE);//待签名字符串
        $params["request_sign"]   = self::getMd5Sign($jsonStr, $payConfig['md5_private_key']);//签名
        //加密
        $encryptData = self::getRsaPublicSign(json_encode($params), $payConfig['public_key']);
        //请求参数
        $req["request_body"]      = urlencode($encryptData);
        $req["interface_version"] = self::getMd5Sign("1.0.0", $app_id);
        $reqDataStr               = static::get_sign($req);
        $header                   = ["Content-type: application/x-www-form-urlencoded\r\n" . "security_header_key: " . $app_id . ""];
        $post                     = [];
        $post['httpHeaders']      = $header;
        $post['data']             = $reqDataStr;
        unset($reqData);
        return $post;
    }

    /***
     * 三方同步返回
     * @param $req 三方返回信息
     * @param $merChantConf 商户配置信息
     * @return array
     */
    public static function outResponse($req, $merChantConf)
    {
        $result = [];
        if ($req) {
            $data = json_decode($req, true);
            if ($data['state_code'] == '200') {
                $result['returnCode'] = 'SUCCESS'; // 固定返回 成功的状态
                $result['returnMsg']  = '代付请求成功,正在出款!'; // 固定返回 成功的信息
            } else {
                $result['returnCode'] = 'ERROR';  // 固定返回 验签失败的状态
                $result['returnMsg']  = isset($data['message']) ? $data['message'] : $req; // 固定返回 验签失败的信息
            }
        } else {
            $result['returnCode'] = 'ERROR'; // 固定返回
            $result['returnMsg']  = '出款失败,无返回信息,请再次尝试！'; // 固定返回
        }
        return $result;
    }

    /**
     * 代付查询返回
     * @param $resData
     * @param $payConf
     * @return array
     */
    public static function OutQueryOrder($resData, $payConf)
    {
        $pt                           = explode('&', $payConf['business_num']);
        $app_id                       = $pt[1];//唯一标识
        $arr                          = [
            'client_num' => $pt[0], //商户号
            'order_num'  => (string)$resData['order'], //订单号
        ];
        $arr['random_str']            = static::generateRandNum(count($arr) + 1);//随机字符串
        $params                       = static::getSignArr($arr, $arr['random_str']);//待签名数组
        $jsonStr                      = json_encode($params);//待签名字符串
        $params['request_sign']       = md5($jsonStr . $payConf['md5_private_key']);//签名;
        $encryptData                  = self::getRsaPublicSign(json_encode($params), $payConf['public_key']);
        $reqData["request_body"]      = urlencode($encryptData);
        $reqData["interface_version"] = md5("1.0.0" . $app_id);
        $reqDataStr                   = self::get_sign($reqData);
        $header                       = ["Content-type: application/x-www-form-urlencoded\r\n" . "security_header_key: " . $app_id . ""];
        $post                         = [];
        $post['httpHeaders']          = $header;
        $post['data']                 = $reqDataStr;
        unset($reqData);
        return $post;
    }

    /***
     * 代付查询返回信息
     * @param $res
     * @param $payConf
     * @return array
     */
    public static function OutQueryRes($res, $payConf)
    {
        $result = [];
        $array  = json_decode($res, true);
        if ($array['remit_state_code'] == 'SUCCESS' && $array['state_code'] == '200') {
            $result['returnUnit'] = '2'; // 三方未返回订单金额时才用，1，为元，2为分
            $result['returnCode'] = 'SUCCESS'; // 固定返回 成功的状态
            $result['returnMsg']  = '出款成功！'; // 固定返回 成功的信息
        } else {
            $result['returnCode'] = $array['state_code']; // 固定返回
            $result['returnMsg']  = $array['msg']; // 固定返回
            //若代付状态失败，停止查询
            if ($array['remit_state_code'] == 'FAILED') {
                $result['returnFail'] = 'QUERY_STOP';
            }
        }
        return $result;
    }

    /**
     * 随机数
     * @param $n
     * @return string
     */
    public static function generateRandNum($n)
    {
        $randArr = [];
        do {
            $num = rand(0, $n - 1);
            if (!in_array($num, $randArr)) {
                array_push($randArr, $num);
            }
        } while (count($randArr) < $n);
        return implode($randArr);
    }

    /**
     * 签名字符串拼接
     * @param $arr
     * @param $randStr
     * @return array
     */
    public static function getSignArr($arr, $randStr)
    {
        ksort($arr);
        $randArr = str_split($randStr);
        $arrKey  = array_keys($arr);
        $signArr = [];
        foreach ($randArr as $value) {
            $k           = $arrKey[$value];
            $signArr[$k] = $arr[$k];
        }
        return $signArr;
    }

    /**
     * 拼接字符串
     * @param $arr
     * @return string
     */
    public static function get_sign($arr)
    {
        $signmd5 = "";
        foreach ($arr as $x => $x_value) {
            if (!$x_value == "" || $x_value == 0) {
                if ($signmd5 == "") {
                    $signmd5 = $signmd5 . $x . '=' . $x_value;
                } else {
                    $signmd5 = $signmd5 . '&' . $x . '=' . $x_value;
                }
            }
        }
        return $signmd5;
    }

}