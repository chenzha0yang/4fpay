<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Changchengzfpay extends ApiModel
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

        //TODO: do something
        self::$unit = 2;
        self::$reqType = 'curl';
        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$method  = 'header';
        self::$isAPP = true;
        if($payConf['pay_way'] == '1'){
            $data['bank_code'] = '906';//支付类型
        }else{ $data['bank_code']= $bank;}
        $data['appid']   = $payConf['business_num'];//商户
        $data['order_no']        = $order;
        $data['amount']      = $amount * 100;//订单金额-以分为单位，;
        $data['product_name']  = 'ipad';

        $data['notify_url'] = $ServerUrl;
        $data['return_url'] = $returnUrl;
        $signStr = self::getSignStr($data, true, true);

        $data['sign'] = strtolower( self::getMd5Sign("{$signStr}&secret=", $payConf['md5_private_key']));
        $jsonData = json_encode($data);
        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ];
        $postData['data']         = $jsonData;
        $postData['httpHeaders']  = $header;
        $postData['trade_no'] = $data['order_no'];
        $postData['amount']  = $data['amount'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['status'] == '1') {
            $data['url']=$data['data']['redirect_url'];
        }
        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $request = $request->all();
        $request['actual_amount'] = $request['actual_amount']/100;
        return $request;
    }
}

