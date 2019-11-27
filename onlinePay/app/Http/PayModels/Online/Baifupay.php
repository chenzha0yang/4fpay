<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Baifupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址


        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }


        self::$payWay   = $payConf['pay_way'];
        self::$unit     = 2;
        self::$reqType = 'curl';
        self::$isAPP=true;
        self::$httpBuildQuery=true;
        $data                 = [];
        $data['merchantNo']   = (string)$payConf['business_num'];//商户编号
        $data['netwayCode']   = (string)$bank;
        $data['randomNum']    = (string)rand(1000, 9999);
        $data['orderNum']     = (string)$order;//商户订单号
        $data['payAmount']    = (string)($amount * 100);// 单位 分
        $data['goodsName']    = (string)'chongzhi';
        $data['callBackUrl']  = (string)$ServerUrl;
        $data['frontBackUrl'] = (string)$returnUrl;
        $data['requestIP']    = (string)self::getIp();

        ksort($data);
        $data['sign'] = strtoupper(md5(self::json_encode($data) . $payConf['md5_private_key'])); #生成签名
        $post         = [
            'paramData' => self::json_encode($data),
            'orderNum'  => $data['orderNum'],
            'payAmount' => $data['payAmount']
        ];

        unset($reqData);
        return $post;
    }

    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);

        return $result;
    }
    public static function json_encode($inPut)
    {
        if (is_string($inPut)) {
            $text = $inPut;
            $text = str_rePlace('\\', '\\\\', $text);
            $text = str_rePlace(
                array("\r", "\n", "\t", "\""),
                array('\r', '\n', '\t', '\\"'),
                $text);
            $text = str_rePlace("\\/", "/", $text);
            return '"' . $text . '"';
        } else if (is_array($inPut) || is_object($inPut)) {
            $arr    = array();
            $is_obj = is_object($inPut) || (array_keys($inPut) !== range(0, count($inPut) - 1));
            foreach ($inPut as $k => $v) {
                if ($is_obj) {
                    $arr[] = self::json_encode($k) . ':' . self::json_encode($v);
                } else {
                    $arr[] = self::json_encode($v);
                }
            }
            if ($is_obj) {
                $arr = str_rePlace("\\/", "/", $arr);
                return '{' . join(',', $arr) . '}';
            } else {
                $arr = str_rePlace("\\/", "/", $arr);
                return '[' . join(',', $arr) . ']';
            }
        } else {
            $inPut = str_rePlace("\\/", "/", $inPut);
            return $inPut . '';
        }
    }

    public static function signOther($model, $data, $payConf)
    {
        $signArr = $data['paramData'];
        $signArr = json_decode($signArr, true);
        $sign    = $signArr['sign'];
        unset($signArr['sign']);
        ksort($signArr);
        $mySign = strtoupper(md5(self::json_encode($signArr) . $payConf['md5_private_key'])); #生成签名
        if (strtoupper($sign) == $mySign && $signArr['resultCode'] == '00') {
            return true;
        } else {
            return false;
        }
    }

    public static function getVerifyResult($request)
    {
        $arr              = self::json_decode($request->all()['paramData']);
        $arr['payAmount'] = $arr['payAmount'] / 100;
        return $arr;
    }
}