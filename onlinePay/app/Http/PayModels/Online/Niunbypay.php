<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Niunbypay extends ApiModel
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
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$isAPP = true;

        $data['login_id']      = $payConf['business_num'];//商户号
        $data['pay_type']      = $bank;//银行编码
        $data['order_type']    = 1;
        $data['order_sn']      = $order;//订单号
        $data['amount']        = sprintf('%.2f', $amount);//订单金额
        $data['send_currency'] = 'cny';
        $data['recv_currency'] = 'cny';
        $data['nonce']         = self::getRandomStr(11, false);
        $data['sign_type']     = 'MD5';
        $data['create_time']   = time();
        $data['create_ip']     = self::getIp();
        $data['notify_url']    = $ServerUrl;

        $data['sign'] = self::datasign($data, $payConf['md5_private_key']);


        unset($reqData);
        return $data;
    }

    //获取随机字符串
    public static function getRandomStr($len, $special = true)
    {
        $chars = array(
            "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        if ($special) {
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }
        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }

    public static function datasign($data, $API_SECRET)
    {
        //排序
        ksort($data);
        //拼接
        $data['sign_type'] = "MD5";
        $sign_str          = "";
        foreach ($data as $k => $v) {
            $sign_str .= ($k . '=' . $v);
            $sign_str .= '&';
        }
        $sign_str = $sign_str . 'api_secret=' . $API_SECRET;
        $sign_md5 = md5($sign_str);
        return $sign_md5;
    }

    public static function SignOther($model, $data, $payConf)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        $mySign = self::datasign($data, $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($mySign)) {
            return true;
        }
        return false;

    }
}