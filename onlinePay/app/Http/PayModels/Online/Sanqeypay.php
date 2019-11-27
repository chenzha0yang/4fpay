<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Sanqeypay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        self::$method = 'get';

        if(!openssl_pkey_get_public($payConf['public_key'])){
            echo '公钥格式错误!请检查';exit();
        }

        $data['account']         = self::$UserName;
        $data['merchantNo']      = $payConf['business_num'];
        $data['type']            = $bank;
        $data['amount']          = $amount;
        $data['key']             = $payConf['md5_private_key'];
        $data['notifyUrl']       = $ServerUrl;
        $data['merchantOrderNo'] = $order;
        $parameter               = 'account=' . $data['account'] . '&amount=' . $data['amount'] . '&key=' . $data['key'] . '&merchantOrderNo=' . $data['merchantOrderNo'] . '&merchantNo=' . $data['merchantNo'] . '&notifyUrl=' . $data['notifyUrl'] . '&type=' . $data['type'] . '8ab211a6536711e893d10242ac11as02';
        $md5Sign                 = md5($parameter);
        $origin_data             = 'account=' . $data['account'] . '&merchantNo=' . $data['merchantNo'] . '&merchantOrderNo=' . $data['merchantOrderNo'] . '&notifyUrl=' . $data['notifyUrl'] . '&type=' . $data['type'] . '&amount=' . $data['amount'] . '&key=' . $data['key'];
        $encodeString            = self::publicEncrypt($origin_data, openssl_pkey_get_public($payConf['public_key']));

        $postData['pr'] = $encodeString;
        $postData['sign'] = $md5Sign;
        unset($reqData);
        return $postData;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $parameter = '';
        if (!empty($data['amount'])) {
            $parameter .= 'amount=' . $data['amount'];
        }
        if (!empty($data['merchantOrderNo'])) {
            $parameter .= '&merchantOrderNo=' . $data['merchantOrderNo'];
        }
        if (!empty($data['platOrderNo'])) {
            $parameter .= '&platOrderNo=' . $data['platOrderNo'];
        }
        if (!empty($data['status'])) {
            $parameter .= '&status=' . $data['status'];
        }
        $parameter .= '&key=' . $payConf['md5_private_key'];
        $signTrue  = md5($parameter);
        if (strtoupper($data['sign']) == strtoupper($signTrue)  && $data['status'] == '1') {
            return true;
        }
        return false;
    }

    public static function publicEncrypt($data = '', $pubkey)
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data, $encrypted, $pubkey) ? base64_encode($encrypted) : null;
    }
}