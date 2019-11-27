<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Dafatwopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = true; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $array = [];

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
        $order = $reqData['order'];
        $amount = $reqData['amount'];
        $bank = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$method = 'header';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];

        $data['paytype'] = (int)$bank;//支付类型
        $data['out_trade_no'] = $order;//订单
        $data['goodsname'] = 'goods_name';//订单
        $data['total_fee'] = sprintf('%.2f', $amount);
        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $data['requestip'] = self::getIp();
        $reqdata['mchid'] = (int)$payConf['business_num'];//商户号
        $reqdata['timestamp'] = time();//商户号
        $reqdata['nonce'] = '9090';
        $signStr = self::getSignStr(array_merge($data, $reqdata), true, true);
        $reqdata['sign'] = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        $reqdata['data'] = $data;

        $post['data'] = json_encode($reqdata);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8',
        );
        $post['out_trade_no'] = $data['out_trade_no'];
        $post['total_fee'] = $data['total_fee'];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($response){

        $data = json_decode($response,true);
        if($data['error'] == '0'){
            $data['payurl'] = $data['data']['payurl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data =  json_decode($arr,  true);
        self::$array = $data;
        return $data;
    }


    public static function SignOther($model, $data, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, false, true);
        $signTrue = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        if (strtolower($sign) == $signTrue ) {
            return true;
        } else {
            return false;
        }

    }


}

