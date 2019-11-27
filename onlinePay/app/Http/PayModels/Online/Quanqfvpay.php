<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Quanqfvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';

    public static $changeUrl = false;
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        self::$isAPP = true;
        //TODO: do something
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        //self::$httpBuildQuery = true;
        self::$changeUrl = true;

        $data['code'] = $payConf['business_num'];//商户号
        $data['order_no'] = $order;//订单号
        //$data['ip'] = $this->get_ip();
        $data['username'] = self::$UserName;
        $data['money'] = sprintf('%.2f',$amount);//订单金额
        $data['type'] = $bank;//银行编码
        $signStr = self::getSignStr($data, true, true);
        $data['sign'] = md5($signStr.'&api_token='.$payConf['public_key']);

        $postData['queryUrl'] = $reqData['formUrl'].'/api/payment/create/web';
        $postData['data'] = http_build_query($data);
        $postData['order_no'] = $data['order_no'];
        $postData['money'] = $data['money'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0') {
            $data['qrCode'] = $data['data']['pay_url'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->getContent();
        $res =  json_decode($arr,true);
        return $res;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $post = file_get_contents('php://input');
        $data = json_decode($post,true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr  = self::getSignStr($data, true, true);
        $signTrue = md5($signStr.'&api_secret='.$payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue)  && $data['result'] == 'success') {
            return true;
        }
        return false;
    }


}