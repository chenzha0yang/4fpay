<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Boluomipay extends ApiModel
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
     * @param array       $reqData 接口传递的参数
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
        self::$method = 'header';
        self::$resType = 'other';

        $data['mch_id']      = $payConf['business_num'];
        $data['mch_number']  = $order;
        $data['pay_mode']    = $bank;
        $data['total']       = (int) ($amount * 100);
        $data['source_ip']   = self::getIp();
        $data['body']        = 'bolm';
        $data['notify_url']  = $ServerUrl;
        $data['nonce_str']   = (string) time();
        $data['sign']        = md5($data['nonce_str'] . '&' . $payConf['md5_private_key']);


        $post['data']        = json_encode($data);
        $post['httpHeaders'] = array(
            'Content-Type: application/json; charset=utf-8'
        );
        $post['mch_number'] = $data['mch_number'];
        $post['total']     = $data['total'];
        unset($reqData);
        return $post;
    }


    public static function getQrCode($response)
    {
        //失败返回示例 {"meta": {}, "code": 1004, "data": {}, "msg": "", "errors": "***"}
        $data = json_decode($response, true);
        if ($data['code'] == '200') {
            $data = $data['data'];//同时传回了pay_url和code  但是WAP只能走pay_url
            if(self::$isAPP){
                $data['payUrl'] =  $data['pay_url'];
            }else{
                $data['qrcode'] =  $data['code'];
            }
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['total'])) {
            $arr['total'] = $arr['total'] / 100;
        }
        return $arr;
    }

    public static function signOther($mod, $data, $payConf)
    {
        $data = file_get_contents('php://input');
        $res = json_decode($data, true);
        $sign = $res['sign'];
        unset($res['sign']);
        $mySign = md5($res['nonce_str'] . '&' . $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($mySign)) {
            return true;
        }
        return false;
    }


}