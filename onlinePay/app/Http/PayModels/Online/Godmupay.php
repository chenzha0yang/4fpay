<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Godmupay extends ApiModel
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
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        self::$unit    = 2;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$method  = 'header';

        $data              = [];
        $data['cid']       = $payConf['business_num'];
        $data['total_fee'] = $amount * 100;
        $data['title']     = 'title';
        $data['attach']    = 'attach';
        $data['platform']  = $bank;
        $data['cburl']     = $returnUrl;
        $data['orderno']   = $order;
        $data['token_url'] = $ServerUrl;
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $value;
        }
        $data['sign']            = strtoupper(md5($signStr . $payConf['md5_private_key']));
        $header                  = array(
            "Accept: application/json",
            "Content-Type: application/json;charset=utf-8",
        );
        $postData['data']        = json_encode($data);
        $postData['httpHeaders'] = $header;
        $postData['total_fee']   = $data['total_fee'];
        $postData['orderno']     = $data['orderno'];
        unset($reqData);
        return $postData;
    }

    /**
     * @param $request
     * @return mixed
     */
    public static function getVerifyResult($request)
    {
        $result              = $request->all();
        $result['total_fee'] = $result['total_fee'] / 100;
        return $result;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $signStr = '';
        ksort($data);
        foreach ($data as $key => $value) {
            if ($key != 'sign') {
                $signStr .= $value;
            }
        }
        $sign = strtoupper(self::getMd5Sign($signStr, $payConf['md5_private_key']));
        if ($sign == strtoupper($data['sign']) && $data['errcode'] == '0') {
            return true;
        }
        return false;
    }
}