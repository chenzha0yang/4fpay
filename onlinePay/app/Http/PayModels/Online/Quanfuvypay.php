<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Quanfuvypay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $reqData = [];

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

        self::$unit     = 2;
        self::$reqType  = 'curl';
        self::$payWay   = $payConf['pay_way'];
        self::$postType = true;
        self::$isAPP    = true;

        $maceCode            = explode('@', $payConf['business_num']);//商户号@商户标识
        if(!isset($maceCode[1])){
            echo '商户号请填入格式为：商户号@商户标识 的商户信息';exit;
        }
        $data['version']     = 'V1.0';
        $data['merchantNum'] = $maceCode[0];
        $data['nonce_str']   = rand(1, 20);
        $data['merMark']     = $maceCode[1];
        $data['client_ip']   = self::getIp();
        $data['orderTime']   = date("Y-m-d H:i:s");
        $data['payType']     = $bank;
        $data['bank_code']   = '';
        if ($payConf['pay_way'] == 1) {
            $data['payType']   = 'B2C';
            $data['bank_code'] = $bank;
        }
        $data['orderNum']  = $order;
        $data['amount']    = $amount * 100;
        $data['body']      = 'xxl';
        $data['signType']  = 'MD5';
        $data['notifyUrl'] = $ServerUrl;
        $data['sign']      = strtoupper(self::getMd5Sign("version={$data['version']}&merchantNum={$data['merchantNum']}&nonce_str={$data['nonce_str']}&merMark={$data['merMark']}&client_ip={$data['client_ip']}&payType={$data['payType']}&orderNum={$data['orderNum']}&amount={$data['amount']}&body={$data['body']}&key=", $payConf['md5_private_key']));
        unset($reqData);
        $post['data']     = $data;
        $post['orderNum'] = $data['orderNum'];
        $post['amount']   = $data['amount'];
        return $post;
    }

    /**
     * 提交json处理
     * @param $post
     * @return false|string
     */
    public static function getRequestByType($post)
    {
        return json_encode($post['data']);
    }

    /**
     * 回调金额处理
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $arr            = $request->getContent();
        $data           = json_decode($arr, true);
        self::$reqData  = $data;
        $data['amount'] = $data['amount'] / 100;
        return $data;
    }

    /**
     * 回调处理
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $data     = self::$reqData;
        $signTrue = strtoupper(self::getMd5Sign("merchantNum={$data['merchantNum']}&orderNum={$data['orderNum']}&amount={$data['amount']}&nonce_str={$data['nonce_str']}&orderStatus={$data['orderStatus']}&key=", $payConf['md5_private_key']));
        if (strtoupper($data['sign']) == $signTrue && $data['orderStatus'] == 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }
}