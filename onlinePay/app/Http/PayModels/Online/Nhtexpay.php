<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Nhtexpay extends ApiModel
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
        //TODO: do something
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;

        $data                  = $post = [];
        $post['payment_type']  = $bank;
        $post['symbol']        = 'AEDT';
        $post['game_order_no'] = $order;
        $post['charge_amount'] = sprintf('%.2f',$amount);
        $post['mark']          = 'nh';

        $data['mchid']         = $payConf['business_num'];
        $data['appid']         = $payConf['business_num'];
        $data['params']        = json_encode($post);
        $data['sign']          = md5($data['mchid'] . $data['appid'] . $data['params'] . $payConf['md5_private_key']);
        $data['game_order_no'] = $post['game_order_no'];
        $data['charge_amount'] = $post['charge_amount'];
        unset($reqData);
        return $data;
    }

    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        if ($result['status'] == '1') {
            $paramsJson = $result['result']['params'];
            $params = json_decode($paramsJson,true);
            $result['payUrl'] = $params['chargeUrl'];
        }
         return $result;
    }
    /***
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $res                 = $request->getContent();
        $data                = json_decode($res, true);
        $params = json_decode($data['params']);
        return (array)$params;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $json       = file_get_contents("php://input");
        $data       = json_decode($json, true);
        $sign = $data['sign'];
        $signTrue = md5($data['mchid'] . $data['appid'] . $data['params'] . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        } else {
            return false;
        }
    }
}