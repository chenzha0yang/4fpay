<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Jinfafupay extends ApiModel
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

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }

        //TODO: do something
        self::$unit    = 2; // 单位 ： 分
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';

        $data['version'] = '1.0';
        $data['mer_id']  = $payConf['business_num'];//商户号
        if ($payConf['pay_way'] == 1) {
            $data['pay_type'] = 'web';
            self::$isAPP = true;
        } else {
            $data['pay_type'] = $bank;//银行编码
        }
        $data['order_id']   = $order;//订单号
        $data['price']      = $amount * 100;//订单金额
        $data['randomid']   = self::getRandomStr(30, false);
        $data['notify_url'] = $ServerUrl;
        $signStr            = 'version=' . $data['version'] . '&mer_id=' . $data['mer_id'] . '&order_id=' . $data['order_id'] . '&price=' . $data['price'] . '&notify_url=' . $data['notify_url'] . '&pay_type=' . $data['pay_type'] . '&randomid=' . $data['randomid'];
        $data['sign']       = strtolower(md5($signStr . "&key=" . $payConf['md5_private_key']));
        $data['return_url'] = $returnUrl;
        $data['attach']     = 'test';
        unset($reqData);
        return $data;
    }

    public static function getQrCode($res)
    {
        $result = json_decode($res, true);
        if($result['code'] == '00'){
            $data = json_decode($result['data'], true);
        }
        $data['code'] = $result['code'];
        $data['message'] = $result['message'];
        return $data;
    }

    public static function SignOther($mod, $datas, $payConf)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $sign = $data['sign'];
        $signStr =  'pay_order='.$data['pay_order'].'&order_id='.$data['order_id'].'&price='.$data['price'].'&pay_type='.$data['pay_type'].'&code='.$data['code'].'&timestamp='.$data['timestamp'];
        $signTrue = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        if (strtolower($sign) == strtolower($signTrue)) {
            return true;
        }
        return false;
    }

    public static function getVerifyResult(Request $request)
    {
        $res = $request->getContent();
        $data = json_decode($res, true);
        if (!isset($data['code'])) {
            $data = $request->all();
        }
        $data['price'] = $data['price'] / 100;
        return $data;
    }

    //获取随机字符串
    public static function getRandomStr($len, $special = true)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
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
}