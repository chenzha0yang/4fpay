<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Suixingfupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';
        self::$resType = 'other';
        //TODO: do something

        $data['out_trade_no']     = $order;//订单号
        $data['goodsname'] = 'apple'; //商品名称
        $data['total_fee']     = $amount;
        $data['paytype']    = $bank; //支付类型
        $data['notify_url']  = $ServerUrl;
        $data['return_url']  = $returnUrl;
        $data['requestip'] = self::getIp();
        $req['mchid'] = $payConf['business_num'];
        $req['timestamp'] = time();
        $req['nonce'] = rand(1000,9999);
        $signStr      = self::getSignStr(array_merge($data,$req), true, true);
        $req['sign'] = strtolower(md5($signStr .'&key='.$payConf['md5_private_key']));
        $req['data'] = $data;
        $post['order']       = $order;
        $post['amount']      = $amount;
        $post['data']        = json_encode($req);
        $post['httpHeaders'] = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($post['data'])
        ];
        unset($reqData);
        return $post;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['error'] == '0') {
            $data['payurl'] = $data['data']['payurl'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $data =  json_decode($arr,true);
        self::$array = $data;
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $data = self::$array;
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = md5($signStr.'&key='. $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }


}