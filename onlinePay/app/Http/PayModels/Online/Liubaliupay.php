<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Liubaliupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

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

        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        $data         = [
            'notifyUrl'    => $ServerUrl,        //异步通知地址
            'outOrderNo'   => $order,            //异步通知地址
            'goodsClauses' => 'dulex',           //商品名称
            'tradeAmount'  => sprintf('%.2f', $amount),     //商品金额
            'code'         => $payConf['business_num'],           //商品金额
            'payCode'      => $bank,             //支付类型
        ];
        $strToSign    = self::getSignStr($data, true, true);
        $data['sign'] = md5($strToSign . '&key=' . $payConf['md5_private_key']);
        unset($reqData);
        return $data;
    }

    /**
     * @param $resp
     * @return mixed
     */
    public static function getQrCode($resp)
    {
        $result = json_decode($resp, true);
        if ($result['payState'] == 'success') {
            $result['url'] = $result['url'];
        } else {
            $result['code'] = '500';
            $result['msg'] = '请求失败';
        }
        return $result;
    }

    /**
     * @param $model
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $data, $payConf)
    {
        $json   = file_get_contents("php://input");
        $result = json_decode(stripslashes($json), true);
        $sign   = $result['sign'];
        unset($result['sign']);
        $signStr = self::getSignStr($result, true, true);
        $mySign  = md5($signStr . '&key=' . $payConf['md5_private_key']);
        if ($mySign == $sign && $result['code'] == '0') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $json                = file_get_contents("php://input");
        $data                = json_decode(stripslashes($json), true);
        $post['outOrderNo']  = $data['outOrderNo'];
        $post['tradeAmount'] = $data['tradeAmount'];
        return $post;

    }
}