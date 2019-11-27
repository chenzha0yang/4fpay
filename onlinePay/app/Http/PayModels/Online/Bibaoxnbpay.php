<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Bibaoxnbpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $changeUrl = false; // 请求url变化

    private static $DesKey = '';
    private static $KeyB = '';
    private static $HeadNum = '6';
    private static $TailNum = '4';
    private static $MerCode = '';
    private static $UserName = '';
    private static $tuBank = '';

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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        self::$reqType  = 'fileGet';
        self::$postType = true;
        self::$resType  = 'other';
        self::$payWay   = $payConf['pay_way'];

        //TODO: do something
        if ($payConf['pay_way'] == '1') {
            self::$tuBank = 'bankcard';
        }
        $merCode = $payConf['business_num'];
        // 注册用户api
        $addUserUrl = "{$payConf['mer_url']}/api/{$merCode}/coin/addUser";
        // 获取地址api
        $getAddressUrl = "{$payConf['mer_url']}/api/{$merCode}/coin/getAddress";
        // 登录api
        $loginUrl  = "{$payConf['mer_url']}/api/{$merCode}/coin/login";
        $regData   = self::addUsers($payConf, $bank); // 注册
        $regResult = self::fileGet($addUserUrl, http_build_query($regData));
        $result    = json_decode($regResult, true);
        echo '正在注册》》》》》';
        echo '<br/>';
        echo '↓↓↓↓↓';
        echo '<br/>';
        if ($result['Success'] === true) {
            $regData   = self::getAddress(); // 获取地址
            $regResult = self::fileGet($getAddressUrl, http_build_query($regData));
            $result    = json_decode($regResult, true);
            echo '正在获取地址》》》》》';
            echo '<br/>';
            echo '↓↓↓↓↓';
            echo '<br/>';
            if ($result['Success'] === true) {
                $regData           = self::loginPay($order, $amount); // 登录提交
                $regData['order']  = $order;
                $regData['amount'] = $amount;

            }
        }
        self::$changeUrl  = true;
        self::$isAPP      = true;
        $data             = [];
        $data['data']     = $regData;
        $data['queryUrl'] = $loginUrl;
        $data['orderNo']  = $order;
        $data['amount']   = $amount;
        unset($reqData);
        return $data;
    }

    public static function getRequestByType($req)
    {
        unset($req['order'], $req['amount']);
        return http_build_query($req);
    }

    public static function getQrCode($response)
    {
        $arr = json_decode($response, true);
        if ($arr['Success']) {
            $arr['url'] = $arr['Data']['Url'] . '/' . $arr['Data']['Token'];
        }
        return $arr;
    }

    public static function SignOther($mod, $data,$payConf)
    {
        $signData = [
            'UserName'    => $data['UserName'],
            'OrderId'     => $data['OrderId'],
            'OrderNum'    => $data['OrderNum'],
            'Type'        => $data['Type'],
            'Coin'        => $data['Coin'],
            'CoinAmount'  => $data['CoinAmount'],
            'LegalAmount' => $data['LegalAmount'],
            'State1'      => $data['State1'],
            'State2'      => $data['State2'],
            'CreateTime'  => $data['CreateTime'],
            'Remark'      => $data['Remark'],
            'Price'       => $data['Price'],
            'Token'       => $data['Token'],
        ];
        ksort($signData);
        $formData = self::buildFormData($signData);
        $sign     = md5(sprintf("%s%s", $formData, $payConf['public_key']));
        if (strcmp($sign, $data['Sign']) == 0 && $data['State1'] == 2 && $data['State2'] == 2) {
            return true;
        } else {
            return false;
        }
    }

    // 注册
    public static function addUsers($payConfig, $bank)
    {

        self::$DesKey  = $payConfig['rsa_private_key'];
        self::$KeyB    = $payConfig['public_key'];
        self::$MerCode = $payConfig['business_num'];
        $data          = [
            'MerCode'   => ['signed' => true],
            'TimeStamp' => ['signed' => false],
            'UserName'  => ['signed' => true],
        ];
        return self::getRequestData($data);
    }

    // 获取地址
    public static function getAddress()
    {
        $data = [
            'MerCode'   => ['signed' => true],
            'TimeStamp' => ['signed' => false],
            'UserType'  => ['signed' => true, 'value' => 1],
            'UserName'  => ['signed' => false],
            'CoinCode'  => ['signed' => true, 'value' => 'DC'],
        ];
        return self::getRequestData($data);
    }

    // 提交数据
    public static function getRequestData($data)
    {
        $desKey             = self::$DesKey;
        $keyB               = self::$KeyB;
        $headNum            = self::$HeadNum;
        $tailNum            = self::$TailNum;
        $config['DesKey']   = self::$DesKey;
        $config['KeyB']     = self::$KeyB;
        $config['HeadNum']  = self::$HeadNum;
        $config['TailNum']  = self::$TailNum;
        $config['MerCode']  = self::$MerCode;
        $config['UserName'] = self::$UserName;

        $timestamp = strval(time());
        $newData   = array();
        $signData  = array();
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $config)) {
                if (array_key_exists('value', $data[$k])) {
                    $newData[$k] = $data[$k]['value'];
                } else {
                    $newData[$k] = $config[$k];
                }
            } else {
                if ($k == 'TimeStamp') {
                    $newData['TimeStamp'] = $timestamp;
                } else {
                    $newData[$k] = $data[$k]['value'];
                }
            }
            if ($data[$k]['signed']) {
                array_push($signData, $newData[$k]);
            }
        }
        array_push($signData, $keyB);
        $formData = self::buildFormData($newData);
        $param    = self::encrypt($formData, $desKey);
        $key      = self::getKey($headNum, $tailNum, $signData);
        $postData = [
            "param" => $param,
            "key"   => $key,
        ];
        return $postData;
    }

    public static function buildFormData($data)
    {
        if (!is_array($data)) {
            return "";
        }

        $result = "";
        foreach ($data as $k => $v) {
            $result .= sprintf("%s=%s&", $k, $v);
        }
        $result = rtrim($result, "&");
        return $result;
    }

    public static function encrypt($data, $key)
    {
        $result = openssl_encrypt($data, "DES-CBC", $key, OPENSSL_RAW_DATA, $key);
        return strtoupper(bin2hex($result));
    }

    public static function decrypt($data, $key)
    {
        $data   = hex2bin(strtolower($data));
        $result = openssl_decrypt($data, "DES-CBC", $key, OPENSSL_RAW_DATA, $key);
        return $result;
    }

    public static function getKey($headNum, $tailNum, $keyData)
    {
        $nowTime = date("Ymd", time());
        $sign    = md5(sprintf("%s%s", join("", $keyData), $nowTime));
        $key     = sprintf("%s%s%s", self::getRandLetter($headNum), $sign, self::getRandLetter($tailNum));
        return $key;
    }

    public static function getRandLetter($length)
    {
        $letters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $length  = min(strlen($letters), $length);
        $result  = substr(str_shuffle($letters), 0, $length);
        return $result;
    }

    // 登录
    public static function loginPay($orderNo, $amount)
    {
        $data = [
            'MerCode'    => ['signed' => true],
            'TimeStamp'  => ['signed' => false],
            'UserName'   => ['signed' => true],
            'Type'       => ['signed' => true, 'value' => 1],
            'Coin'       => ['signed' => false, 'value' => 'DC'],
            'Amount'     => ['signed' => false, 'value' => $amount],
            'OrderNum'   => ['signed' => true, 'value' => $orderNo],
            'PayMethods' => ['signed' => false, 'value' => self::$tuBank],
        ];
        return self::getRequestData($data);
    }

    public static function fileGet($url, $postData, $method = 'POST', $header = '', $time = 20)
    {
        $opts    = array(
            'http' => array(
                'method'  => $method,
                'header'  => $header ? $header : 'Content-type:application/x-www-form-urlencoded',
                'content' => $postData,
                'timeout' => $time,
            ),
        );
        $context = stream_context_create($opts);
        $html    = file_get_contents($url, false, $context);
        return $html;
    }
}
